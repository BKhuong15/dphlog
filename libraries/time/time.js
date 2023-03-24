
/**
 * Created by Austin 2/6/2023
 */
var state = 'now';
var main_interval;
$(document).ready(function()
{
  // Time conversions from milliseconds.
  const MILI_SEC = 1000;

  let $form = $('#time_converter');
  let $right_form = $('.right-form');
  let $date_and_epoch = $('.date-and-epoch');

  // Initialize clocks.
  clockStep();
  /*******************
   * Button to stop time and convert to inputted time.
   *******************/
  $form.find('.convert-button input').click(function(e)
  {
    e.preventDefault();
    clearInterval(main_interval);

    state = 'then';
    clockStep();

    // Add text to show clocks are paused.
    $date_and_epoch.find('#clock-state').text('Clocks Paused');

    let timezone_value = $form.find('.field.timezone select').val();

    // Remove red border from everything, then add it to desired conversion clock.
    $('.current-timezone').removeClass('current-timezone');
    $('.clock-wrap#' + machineName(timezone_value)).addClass('current-timezone');
  });

  /**********************************************************
   * Now/reset button, resets the clocks to the current time
   **********************************************************/
  $right_form.find('.now-button input').click(function(e)
    {
      e.preventDefault();
      state = 'now';

      // Get local timezone.
      let user_time_zone = new Intl.DateTimeFormat().resolvedOptions().timeZone;

      // Remove Clocks Paused text after click.
      $date_and_epoch.find('#clock-state').text('');

      // // Clear left form.
      $form.find('.field.timestamp input').val('');
      $form.find('.field.time input').val('');
      $form.find('.field.date input').val('');
      $form.find('.field.timezone select').val('America/New_York');

      //Add red border to the local timezone clock.
      $('.current-timezone').removeClass('current-timezone');
      $('.clock-wrap#' + machineName(user_time_zone)).addClass('current-timezone');
      main_interval = setInterval(clockStep, MILI_SEC);
    }).click();
});

/**
 * Main loop for the clocks. Updates every second in the setInterval loop
 */
function clockStep()
{
  let datetime;
  let date_time;
  let epoch;

  // Set up options for date time format.
  let options =
    {
      day: 'numeric',
      month: 'numeric',
      year: 'numeric'
    };

  // "Now" means get current time from client side.
  // If the button is pressed (state is now), then set date to current time.
  if (state === 'now')
  {
    datetime = new Date();
    date_time = new Intl.DateTimeFormat('en-US', options).format(datetime);
    epoch = Math.floor(Date.now() / 1000);
  }
  // Otherwise set the date to the inputted time.
  else
  {
    let $form = $('#time_converter');

    let timestamp = $form.find('.field.timestamp input').val();
    let time_value = $form.find('.field.time input').val();
    let date_value = $form.find('.field.date input').val();
    let timezone = $form.find('.field.timezone select').val();

    // If timestamp is empty, use input from date/time section.
    if (timestamp === "")
    {
      datetime = parseDateTimeInputData(time_value, date_value, timezone);
      date_time = new Intl.DateTimeFormat('en-US', options).format(datetime);

      epoch = Math.floor(datetime.getTime() / 1000);
    }
    // Otherwise use the input from the timestamp field.
    else
    {
      datetime = new Date(timestamp * 1000);
      date_time = new Intl.DateTimeFormat('en-US', options).format(datetime);
      epoch = Math.floor(datetime.getTime() / 1000);
    }
  }

  // Full dictionary of iana timezones => general zone. matches with dict in php file.
  const zone_dict =
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

  // Create datetimeformat object based on user's machine's current time.
  $('.current-date').html(date_time);

  // Get # of miliseconds since jan 1, 1970 and divide by 1000 to get seconds.
  $('.unix-time').html(epoch);

  // Update display time and clock hands for each timezone.
  for (let key in zone_dict)
  {
    startClockHands(datetime, key);
  }
}

/**
 * @param datetime: js Date object
 * @param iana_timezone: iana timezone string in "America/New_York" format
 * Updates the clock hands based on the time of the desired timezone.
 */
function startClockHands(datetime, iana_timezone)
{
  let options =
    {
      hour: 'numeric',
      minute: 'numeric',
      second: 'numeric',
      timeZone: iana_timezone
    };

  let full = new Intl.DateTimeFormat('en-US', options).format(datetime);

  //Get hr, mins, sec from the desired time zone, outputs as string type.
  let seconds = new Intl.DateTimeFormat('en-US', {second: 'numeric', timeZone: iana_timezone, hour12: false}).format(datetime);
  let mins = new Intl.DateTimeFormat('en-US', {minute: 'numeric', timeZone: iana_timezone, hour12: false}).format(datetime);
  let hour = new Intl.DateTimeFormat('en-US', {hour: 'numeric', timeZone: iana_timezone, hour12: false}).format(datetime);

  //Create jquery selector and parse the id based on the ianaTimeZone (same as in php).
  let $clock = $('#' + machineName(iana_timezone));

  //Calculate sec,min,hr and transform each hand based on the value.
  //Finds the given id for each timezone, and updates the hands on each clock.
  const seconds_degrees = ((seconds / 60) * 360) + 90;
  $clock.find('.second-hand').css('transform', 'rotate(' + seconds_degrees + 'deg');

  const mins_degrees = ((mins / 60) * 360) + ((seconds/60)*6) + 90;
  $clock.find('.min-hand').css('transform', 'rotate(' + mins_degrees + 'deg');

  const hour_degrees = ((hour / 12) * 360) + ((mins/60)*30) + 90;
  $clock.find('.hour-hand').css('transform', 'rotate(' + hour_degrees + 'deg');

  //Pass data into .php file for web interface display.
  $clock.find('.time').text(full);
}

/**
 * @param time: time string in "12:00 am" format
 * @param date: date in "m/d/y" format
 * @param timezone: timezone from form
 * @returns {Date}: Date() object for intl use
 * Takes user's input from form and parses input into intl library readable format.
 */
function parseDateTimeInputData(time, date, timezone)
{
  //Parse time string.
  let parsed_time = time.match(/(\d{2}):(\d{2}) (am|pm)/);
  let hours = parsed_time[1];
  let minutes = parsed_time[2];
  let ampm = parsed_time[3];

  //Distinguish between am/pm.
  if (ampm === 'pm' && hours !== '12')
  {
    hours = (parseInt(hours, 10) + 12).toString();
  } else if (ampm === 'am' && hours === '12')
  {
    hours = '00';
  }

  //Parse date string.
  let parsed_date = date.match(/(\d{2})\/(\d{2})\/(\d{4})/);
  let month = parseInt(parsed_date[1]) - 1;
  let day = parsed_date[2];
  let year = parsed_date[3];

  //Convert to Date() object.
  let temp = new Date(year, month, day, hours, minutes);

  let options =
  {
    timeZone: timezone,
    hour: 'numeric',
    timeZoneName: 'shortOffset'
  }

  // +4 hardcoded to offset UTC time to EST.
  let selected_offset = new Intl.DateTimeFormat('en-US', options).format(temp);
  selected_offset = parseInt(selected_offset.substring(selected_offset.indexOf('GMT-') + 4));

  let datetime = new Date(Date.UTC(year, month, day, hours, minutes));
  datetime.setHours(datetime.getHours() + selected_offset);

  return datetime;
}


// $(document).ready(function()
// {
//   // Pricing variables.
//   let qemr_full_time_price = 69;
//   let other_full_time_price = 180;
//
//   let qemr_pt_provider_price = 35;
//   let other_pt_provider_price = 180;
//
//   let qemr_personnel_price = 0;
//   let other_personnel_price = 100; // TBD
//
//   let qemr_connect_price = 0;
//   let other_connect_price = 0;
//
//   let qemr_calls_price = 0.10;
//   let other_calls_price = 10; // TBD
//
//   // Input IDs.
//   let $full_time_provider_input = $("#provider-input");
//   let $qemr_provider_value = $("#qemr-provider-value");
//   let $other_provider_value = $("#other-provider-value");
//
//   let $part_time_provider_input = $("#pt-provider-input");
//   let $qemr_pt_provider_value = $("#qemr-pt-provider-value");
//   let $other_pt_provider_value = $("#other-pt-provider-value");
//
//   let $support_input = $("#support-input");
//   let $qemr_support_value = $("#qemr-support-value");
//   let $other_support_value = $("#other-support-value");
//
//
//   let $connect_input = $("#connect-input");
//   let $qemr_connect_value = $("#qemr-connect-value");
//   let $other_connect_value = $("#other-connect-value");
//
//   let $reminder_calls_input = $("#reminder-calls-input");
//   let $qemr_reminder_calls_value = $("#qemr-calls-value");
//   let $other_reminder_calls_value = $("#other-calls-value");
//
//   let $qemr_totals_cell = $("#qemr-total-value");
//   let $other_total_cell = $("#other-total-value")
//
//   // Prices * input values.
//   let qemr_ft_amount = 0;
//   let qemr_pt_amount = 0;
//   let qemr_support_amount = 0;
//   let qemr_connect_amount = 0;
//   let qemr_calls_amount = 0;
//
//   let other_ft_amount = 0;
//   let other_pt_amount = 0;
//   let other_support_amount = 0;
//   let other_connect_amount = 0
//   let other_calls_amount = 0;
//
//   // Full time provider cells.
//   qemr_ft_amount = calculatePrices($full_time_provider_input, qemr_full_time_price, $qemr_provider_value);
//   other_ft_amount = calculatePrices($full_time_provider_input, other_full_time_price, $other_provider_value);
//
//   $full_time_provider_input.change(function()
//   {
//     qemr_ft_amount = calculatePrices($full_time_provider_input, qemr_full_time_price, $qemr_provider_value);
//     other_ft_amount = calculatePrices($full_time_provider_input, other_full_time_price, $other_provider_value);
//     $qemr_totals_cell.trigger("refresh");
//     $other_total_cell.trigger("refresh");
//   });
//
//   // Part time provicer cells.
//   qemr_pt_amount = calculatePrices($part_time_provider_input, qemr_pt_provider_price, $qemr_pt_provider_value);
//   other_pt_amount = calculatePrices($part_time_provider_input, other_pt_provider_price, $other_pt_provider_value);
//
//   $part_time_provider_input.change(function()
//   {
//     qemr_pt_amount = calculatePrices($part_time_provider_input, qemr_pt_provider_price, $qemr_pt_provider_value);
//     other_pt_amount = calculatePrices($part_time_provider_input, other_pt_provider_price, $other_pt_provider_value);
//     $qemr_totals_cell.trigger("refresh");
//     $other_total_cell.trigger("refresh");
//   });
//
//
//   // Support Personnel cells.
//   qemr_support_amount = calculatePrices($support_input, qemr_personnel_price, $qemr_support_value);
//   other_support_amount = calculatePrices($support_input, other_personnel_price, $other_support_value);
//
//   $support_input.change(function()
//   {
//     qemr_support_amount = calculatePrices($support_input, qemr_personnel_price, $qemr_support_value);
//     other_support_amount = calculatePrices($support_input, other_personnel_price, $other_support_value);
//     $qemr_totals_cell.trigger("refresh");
//     $other_total_cell.trigger("refresh");
//   });
//
//   // Connect cells.
//   $qemr_connect_value.text('$' + qemr_connect_price.toFixed(2));
//   $other_connect_value.text('$' + other_connect_price.toFixed(2));
//
//   $connect_input.click(function()
//   {
//     if ($connect_input.is(":checked")){
//       qemr_connect_price = 0;
//       other_connect_price = 399;
//     }
//     else
//     {
//       qemr_connect_price = 0;
//       other_connect_price = 0;
//     }
//
//     $qemr_connect_value.text("$" + qemr_connect_price.toFixed(2));
//     $other_connect_value.text("$" + other_connect_price.toFixed(2));
//     qemr_connect_amount = qemr_connect_price;
//     other_connect_amount = other_connect_price;
//
//     $qemr_totals_cell.trigger("refresh");
//     $other_total_cell.trigger("refresh");
//
//   });
//
//   // Reminder calls cells.
//   qemr_calls_amount = calculatePrices($reminder_calls_input, qemr_calls_price, $qemr_reminder_calls_value);
//   other_calls_amount = calculatePrices($reminder_calls_input, other_calls_price, $other_reminder_calls_value);
//
//   $reminder_calls_input.change(function()
//   {
//     qemr_calls_amount = calculatePrices($reminder_calls_input, qemr_calls_price, $qemr_reminder_calls_value);
//     other_calls_amount = calculatePrices($reminder_calls_input, other_calls_price, $other_reminder_calls_value);
//     $qemr_totals_cell.trigger("refresh");
//     $other_total_cell.trigger("refresh");
//   });
//
//   // Total cells.
//
//   $qemr_totals_cell.on("refresh", function()
//   {
//     let total = (qemr_ft_amount + qemr_pt_amount + qemr_calls_amount + qemr_support_amount + qemr_connect_amount);
//     $qemr_totals_cell.text('$' + total.toFixed(2));
//   });
//
//   $qemr_totals_cell.trigger("refresh");
//
//   $other_total_cell.on("refresh", function()
//   {
//     let other_total = other_ft_amount + other_pt_amount + other_calls_amount + other_support_amount + other_connect_amount;
//     $other_total_cell.text("$" + other_total.toFixed(2));
//   });
//
//   $other_total_cell.trigger("refresh");
//
//
// });
//
// function calculatePrices(input_cell, price, output_cell)
// {
//   // Get user input.
//   let cell_value = parseFloat(input_cell.val());
//   // Calculate feature price.
//   let amount = cell_value * (price.toFixed(2));
//
//   // Update calculated price on screen.
//   output_cell.text("$" + amount.toFixed(2));
//
//   return amount;
// }
