
/**
 * Created by Austin 2/6/2023
 */
var state = 'now';
var main_interval;
$(document).ready(function()
{
const ONE_SECOND = 1000;
const ONE_MINUTE = 60000;

let $form = $('#time_converter');
let $rightForm = $('.right-form');

//Button to stop time and convert to inputted time.
$form.find('.convert-button input').click(function(e)
{
e.preventDefault();
clearInterval(main_interval);

state = 'then';
start();


let timezoneValue = $form.find('.field.timezone select').val();

//Remove red border from everything, then add it to desired conversion clock.
$(".current-timezone").removeClass("current-timezone");
$('.clock-wrap#' + machineName(timezoneValue)).addClass('current-timezone');
});

// //Button to restart clock timers, upon page load, click button to start clocks.
// $rightForm.find('.now-button input').click(function(e)
// {
//     //prevent default behavior from function (in this case its reload the page when submit is pressed)
//     e.preventDefault();
//     state = 'now';
//     start();
//
//     let user_time_zone = new Intl.DateTimeFormat().resolvedOptions().timeZone;
//
//     //Add red border to the local timezone clock.
//     $(".current-timezone").removeClass("current-timezone");
//     $('.clock-wrap#' + machineName(user_time_zone)).addClass('current-timezone');
//     main_interval = setInterval(start, ONE_SECOND);
//
//     //getCurrentTimeAjax();
//
// }).click();

$rightForm.find('.now-button-ajax input').click(function(e)
{
e.preventDefault();
state = 'now';

getCurrentTimeAjax();

//clear left form
$form.find('input[name="timestamp"]').val('');
$form.find('input[name="time"]').val('');
$form.find('input[name="date"]').val('');
$form.find('select[name="timezone"]').val('');

let user_time_zone = new Intl.DateTimeFormat().resolvedOptions().timeZone;
//Add red border to the local timezone clock.
$(".current-timezone").removeClass("current-timezone");
$('.clock-wrap#' + machineName(user_time_zone)).addClass('current-timezone');
main_interval = setInterval(start, ONE_SECOND);

main_interval = setInterval(getCurrentTimeAjax, ONE_SECOND);
}).click();

//Verify user local time is equal to the server time in epoch time
setInterval(verifyCurrentTime, ONE_MINUTE);

});

function start()
{
  let datetime;
  let date_time;
  let epoch;

  //Set up options for date time format.
  let options =      {          day: 'numeric', month: 'numeric', year: 'numeric'      };

  // "Now" means get current time from client side.
  //If the button is pressed (state is now), then set date to current time.
  if (state === 'now')
  {
    datetime = new Date();
    date_time = new Intl.DateTimeFormat('en-US', options).format(datetime);
    epoch = Math.floor(Date.now() / 1000);
  }
  //Otherwise set the date to the inputted time.
  else
  {
    let $form = $('#time_converter');

    let timestamp = $form.find('input[name="timestamp"]').val();
    let timeValue = $form.find('input[name="time"]').val();
    let dateValue = $form.find('input[name="date"]').val();
    let timezone = $form.find('select[name="timezone"]').val();

    if (timestamp === "")
    {
      datetime = parseDateTimeInputData(timeValue, dateValue, timezone);
      date_time = new Intl.DateTimeFormat('en-US', options).format(datetime);

      epoch = Math.floor(datetime.getTime() / 1000);
    }
    else
    {
      datetime = new Date(timestamp * 1000);
      date_time = new Intl.DateTimeFormat('en-US', options).format(datetime);
      epoch = Math.floor(datetime.getTime() / 1000);
    }
  }

  //Full dictionary of iana timezones => general zone. matches with dict in php file.
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

  //Create datetimeformat object based on user's machine's current time.
  $('.current-date').html(date_time);

  //Get # of miliseconds since jan 1, 1970 and divide by 1000 to get seconds.
  $('.unix-time').html(epoch);

  //Update display time and clock hands for each timezone.
  for (let key in zoneDict)
  {
    startClockHands(datetime, key);
  }
}

function startClockHands(datetime, ianaTimeZone)
{
  let options =
    {
      hour: 'numeric',
      minute: 'numeric',
      second: 'numeric',
      timeZone: ianaTimeZone
    };

  let full = new Intl.DateTimeFormat('en-US', options).format(datetime);

  //Get hr, mins, sec from the desired time zone, outputs as string type.
  let seconds = new Intl.DateTimeFormat('en-US', {second: 'numeric', timeZone: ianaTimeZone, hour12: false}).format(datetime);
  let mins = new Intl.DateTimeFormat('en-US', {minute: 'numeric', timeZone: ianaTimeZone, hour12: false}).format(datetime);
  let hour = new Intl.DateTimeFormat('en-US', {hour: 'numeric', timeZone: ianaTimeZone, hour12: false}).format(datetime);

  //Create jquery selector and parse the id based on the ianaTimeZone (same as in php).
  let $clock = $('#' + machineName(ianaTimeZone));

  //Calculate sec,min,hr and transform each hand based on the value.
  //Finds the given id for each timezone, and updates the hands on each clock.
  const secondsDegrees = ((seconds / 60) * 360) + 90;
  $clock.find('.second-hand').css('transform', 'rotate(' + secondsDegrees + 'deg');

  const minsDegrees = ((mins / 60) * 360) + ((seconds/60)*6) + 90;
  $clock.find('.min-hand').css('transform', 'rotate(' + minsDegrees + 'deg');

  const hourDegrees = ((hour / 12) * 360) + ((mins/60)*30) + 90;
  $clock.find('.hour-hand').css('transform', 'rotate(' + hourDegrees + 'deg');

  //Pass data into .php file for web interface display.
  $clock.find('.time').text(full);
}

/**
 * @param string: string of class/id
 * @returns {string}: machine-readable string
 * turns strings into machine-readable strings, ie timezone string -> readable class
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
function parseDateTimeInputData(time, date, timezone)
{
  //Parse time string.
  let parsedTime = time.match(/(\d{2}):(\d{2}) (am|pm)/);
  let hours = parsedTime[1];
  let minutes = parsedTime[2];
  let ampm = parsedTime[3];

  //Distinguish between am/pm.
  if (ampm === 'pm' && hours !== '12') {
    hours = (parseInt(hours, 10) + 12).toString();
  } else if (ampm === 'am' && hours === '12') {
    hours = '00';
  }

  //Parse date string.
  let parsedDate = date.match(/(\d{2})\/(\d{2})\/(\d{4})/);
  let month = parseInt(parsedDate[1]) - 1;
  let day = parsedDate[2];
  let year = parsedDate[3];

  //Convert to Date() object.
  let temp = new Date(year, month, day, hours, minutes);

  let options =
    {
      timeZone: timezone,
      hour: 'numeric',
      timeZoneName: 'shortOffset'
    }

  let selected_offset = new Intl.DateTimeFormat('en-US', options).format(temp);
  selected_offset = parseInt(selected_offset.substring(selected_offset.indexOf('GMT-') + 4));

  let datetime = new Date(Date.UTC(year, month, day, hours, minutes));
  datetime.setHours(datetime.getHours() + selected_offset);

  return datetime;
}

function returnCurrentTimeBasedOnAjax(serverEpoch)
{
  let options =
    {
      day: 'numeric',
      month: 'numeric',
      year: 'numeric'
    };

  var date = new Date(serverEpoch * 1000);
  let date_time_format = new Intl.DateTimeFormat('en-US', options).format(date);
  // console.log(serverEpoch);
  // console.log(date_time_format);
  // console.log(date);

  //Full dictionary of iana timezones => general zone. matches with dict in php file.
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

  //Create datetimeformat object based on user's machine's current time.
  $('.current-date').html(date_time_format);

  //Get # of miliseconds since jan 1, 1970 and divide by 1000 to get seconds.
  $('.unix-time').html(serverEpoch);

  //Update display time and clock hands for each timezone.
  for (let key in zoneDict)
  {
    startClockHands(date, key);
  }
}

function getCurrentTimeAjax()
{
  $.ajax({
    type: "GET",
    url: "/ajax/time",
    dataType: "json",
    success: function(response) {
    // console.log("time retrieved from ajax: " + response['currentServerTime']);
    // //default time zone is mountain time
    // console.log("local timezone retrieved from ajax: " + response['localTimezone']);
    returnCurrentTimeBasedOnAjax(response['currentServerTime']);

    },
    error: function(xhr, status, error) {
    console.log("Ajax function error");
    console.log(error);
    }
  });
}

function verifyCurrentTime()
{
  $.ajax({
    type: "GET",
    url: "/ajax/time",
    dataType: "json",
    success: function(response)
      {
      //Get user's local time, convert to seconds, then round down/
      let localTime = new Date().getTime() / 1000;
      localTime = Math.floor(localTime);

      if(localTime !== response['currentServerTime'])
      {
        console.log("Warning! User local time and server time are not equal.");
        console.log("User time: " + localTime);
        console.log("Server time: " + response['currentServerTime']) ;
      }
      },
    error: function(xhr, status, error)
      {
      console.log("Ajax function error");
      console.log(error);
      }
  });
}

$form.on('refresh', '.name-phone_numbers tbody', function()
{
  let url = '/ajax/patient/phone';
  let data =
  {
    operation: 'view',
    patient_id: $('.name-id input').val()
  };
  let $phone_list = $('.name-phone_numbers tbody');
  $phone_list.parents('.table-wrapper').addClass('loading');
  $.get(url, data, function(response)
  {
    $phone_list.html(response['data']);
    $phone_list.parents('.table-wrapper').removeClass('loading');
    qfn.growl_message(response['message']);
  }, 'json').error(ajaxErrorHandler);
});