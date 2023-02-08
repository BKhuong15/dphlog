/**
 * Created by Austin 2/6/2023
 */

$(document).ready(function () {
    //run function and update every second
    start();
    setInterval(start, 1000);

    startClockHands()
    setInterval(startClockHands, 1000)

});

function start() {
    //variables, gets current time from client side
    const curr_time = new Date();

    let ianaTimeZones = ["America/New_York", "America/Chicago", "America/Denver", "America/Los_Angeles", "America/Anchorage", "Pacific/Honolulu"]

    //set up options for date time format
    let options = {
        year: 'numeric', month: 'long', day: 'numeric',
        hour: 'numeric', minute: 'numeric', second: 'numeric',
        timeZoneName :'longGeneric'
    };

    //create datetimeformat object based on user's machine's current time
    let date_time = new Intl.DateTimeFormat('en-US', options).format(curr_time);
    $('.human-time').html("Your current time is: " + date_time);

    //get # of miliseconds since jan 1, 1970 and divide by 1000 to get seconds
    let epoch = Math.floor(Date.now() / 1000);
    // console.log(epoch)
    $('.unix-time').html("The current epoch time is: " + epoch);

    let phpArrayVariables = [".eastern", ".central", ".mountain", ".pacific", ".alaska", ".hawaii"]

    //create different objects with different time zones for display
    options.timeZone = ianaTimeZones[0];
    let eastern = new Intl.DateTimeFormat('en-US', options).format(curr_time);
    options.timeZone = ianaTimeZones[1];
    let central = new Intl.DateTimeFormat('en-US', options).format(curr_time);
    options.timeZone = ianaTimeZones[2];
    let mountain = new Intl.DateTimeFormat('en-US', options).format(curr_time);
    options.timeZone = ianaTimeZones[3];
    let pacific = new Intl.DateTimeFormat('en-US', options).format(curr_time);
    options.timeZone = ianaTimeZones[4];
    let alaska = new Intl.DateTimeFormat('en-US', options).format(curr_time);
    options.timeZone = ianaTimeZones[5];
    let hawaii = new Intl.DateTimeFormat('en-US', options).format(curr_time);

    //pass this new data into hmtl format for web interface display
    $('.eastern').html(eastern);
    $('.central').html(central);
    $('.mountain').html(mountain);
    $('.pacific').html(pacific);
    $('.alaska').html(alaska);
    $('.hawaii').html(hawaii);

}
function startClockHands(){
    const now = new Date();
    const secondHand = document.querySelector('.second-hand');
    const minsHand = document.querySelector('.min-hand');
    const hourHand = document.querySelector('.hour-hand');

    const seconds = now.getSeconds();
    const secondsDegrees = ((seconds / 60) * 360) + 90;
    secondHand.style.transform = `rotate(${secondsDegrees}deg)`;

    const mins = now.getMinutes();
    const minsDegrees = ((mins / 60) * 360) + ((seconds/60)*6) + 90;
    minsHand.style.transform = `rotate(${minsDegrees}deg)`;

    const hour = now.getHours();
    const hourDegrees = ((hour / 12) * 360) + ((mins/60)*30) + 90;
    hourHand.style.transform = `rotate(${hourDegrees}deg)`;
}
