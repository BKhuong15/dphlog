/**
 * Created by Austin 2/6/2023
 */
var state = 'now';
var main_interval;

$(document).ready(function()
{
    var ONE_SECOND = 1000;

    let $form = $('#time_converter');


    //button to stop time and convert to inputted time
    $form.find('.convert-button input').click(function(e)
    {
        e.preventDefault();
        clearInterval(main_interval);

        state = 'then';
        start();

        let timezoneValue = $form.find('.field.timezone select').val();

        //remove red border from everything, then add it to desired conversion clock
        $(".current-timezone").removeClass("current-timezone");
        $('.clock-wrap#' + machineName(timezoneValue)).addClass('current-timezone');
    });

    //button to restart clock timers, upon page load, click button to start clocks
    $form.find('.now-button input').click(function(e)
    {
        e.preventDefault();
        state = 'now';
        start();

        let user_time_zone = new Intl.DateTimeFormat().resolvedOptions().timeZone;

        //add red border to the local timezone clock
        $(".current-timezone").removeClass("current-timezone");
        $('.clock-wrap#' + machineName(user_time_zone)).addClass('current-timezone');
        main_interval = setInterval(start, ONE_SECOND);
        getCurrentTimeAjax();

    }).click();
});

function start()
{
    let datetime;

    // "now" means get current time from client side.
    //if the button is now pressed (state is now) then set date to current time
    if (state === 'now')
    {
        datetime = new Date();
    }
    //otherwise set the date to the inputted time
    else
    {
        let $form = $('#time_converter');
        let timeValue = $form.find('input[name="time"]').val();
        let dateValue = $form.find('input[name="date"]').val();

        datetime = parseDateTimeInputData(timeValue, dateValue);
    }

    //full dictionary of iana timezones => general zone. matches with dict in php file
    const zoneDict =
    {
        'America/New_York': 'Eastern',
        'America/Chicago': 'Central',
        'America/Denver': 'Mountain',
        //'America/Phoenix': 'Mountain (Arizona)',
        'America/Los_Angeles': 'Pacific',
        'America/Anchorage': 'Alaska',
        'America/Adak': 'Hawaii',
        //'Pacific/Honolulu': 'Hawaii (No Daylight Savings)'
    };

    //set up options for date time format
    let options =
    {
        day: 'numeric', month: 'numeric', year: 'numeric'
    };

    //create datetimeformat object based on user's machine's current time
    let date_time = new Intl.DateTimeFormat('en-US', options).format(datetime);
    $('.current-date').html(date_time);

    //get # of miliseconds since jan 1, 1970 and divide by 1000 to get seconds
    let epoch = Math.floor(Date.now() / 1000);
    $('.unix-time').html(epoch);

    //update display time and clock hands for each timezone
    for (let key in zoneDict){
        startClockHands(datetime, key);
    }
}

function startClockHands(CURRENT_TIME, ianaTimeZone)
{
    let options =
    {
        hour: 'numeric',
        minute: 'numeric',
        second: 'numeric',
        timeZone: ianaTimeZone
    };

    let full = new Intl.DateTimeFormat('en-US', options).format(CURRENT_TIME);

    //get hr, mins, sec from the desired time zone, outputs as string type
    let seconds = new Intl.DateTimeFormat('en-US', {second: 'numeric', timeZone: ianaTimeZone, hour12: false}).format(CURRENT_TIME);
    let mins = new Intl.DateTimeFormat('en-US', {minute: 'numeric', timeZone: ianaTimeZone, hour12: false}).format(CURRENT_TIME);
    let hour = new Intl.DateTimeFormat('en-US', {hour: 'numeric', timeZone: ianaTimeZone, hour12: false}).format(CURRENT_TIME);

    //create jquery selector and parse the id based on the ianaTimeZone (same as in php)
    let $clock = $('#' + machineName(ianaTimeZone));

    //calculate sec,min,hr and transform each hand based on the value
    //finds the given id for each timezone, and updates the hands on each clock
    const secondsDegrees = ((seconds / 60) * 360) + 90;
    $clock.find('.second-hand').css('transform', 'rotate(' + secondsDegrees + 'deg');

    const minsDegrees = ((mins / 60) * 360) + ((seconds/60)*6) + 90;
    $clock.find('.min-hand').css('transform', 'rotate(' + minsDegrees + 'deg');

    const hourDegrees = ((hour / 12) * 360) + ((mins/60)*30) + 90;
    $clock.find('.hour-hand').css('transform', 'rotate(' + hourDegrees + 'deg');

    //pass data into .php file for web interface display
    $clock.find('.time').text(full);
}

/**
 * @param string: string of class/id
 * @returns {string}: machine readable string
 * turns strings into machine readable strings, ie timezone string -> readable class
 */
function machineName(string)
{
    string = string.replace(/\s/g, '_');
    string = string.toLowerCase();
    string = string.replace(/[^A-Za-z0-9_-]/, '_');
    return string;
}

/**
 * @param time: time string in "12:00 am" format
 * @param date: date in "m/d/y" format
 * @returns {Date}: Date() object for intl use
 * takes user's input from form and parses input into intl library readable format
 */
function parseDateTimeInputData(time, date)
{
    //parse time string
    let parsedTime = time.match(/(\d{2}):(\d{2}) (am|pm)/);
    let hours = parsedTime[1];
    let minutes = parsedTime[2];
    let ampm = parsedTime[3];

    //distinguish between am/pm
    if (ampm === 'pm' && hours !== '12') {
        hours = (parseInt(hours, 10) + 12).toString();
    } else if (ampm === 'am' && hours === '12') {
        hours = '00';
    }

    //parse date string
    let parsedDate = date.match(/(\d{2})\/(\d{2})\/(\d{4})/);
    let month = (parseInt(parsedDate[1], 10) - 1).toString();
    let day = parsedDate[2];
    let year = parsedDate[3];

    //convert to Date() object
    let datetime = new Date(year, month, day, hours, minutes);
    return datetime;
}

function getCurrentTimeAjax()
{
    $.ajax({
        type: "GET",
        url: "/ajax/time",
        dataType: "json",
        success: function(response) {
            console.log("time retrieved from php: " + response['data']);
        },
        error: function(xhr, status, error) {
            console.log("Ajax function error");
            console.log(error);
        }
    });
}
