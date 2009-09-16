{* debug *}

<script type="text/javascript">
$(function(){ldelim}

      // JSON object
      var data = {$datesJSON};
      // Date object
      var defaultDate = new Date();
      defaultDate.setFullYear({$defaultDate[0]}, {$defaultDate[1]}, {$defaultDate[2]});
      // Array to hold dates.
      // Each date is separated into day, month and year and saved in
      // its respective array. The separation is done on server side
      // to keep the logic of date syntax parsing away from client
      // side.
      var days = [];
      var months = [];
      var years = [];

      // Evaluate JSON object
      for (x = 0; x < data.dates.length; x++) {ldelim}
          // Put results into array
          days.push(data.dates[x].day);
          months.push(data.dates[x].month);
          years.push(data.dates[x].year);
      {rdelim}

      // Define date picker config object
      var pickerOpts = {ldelim}
          {if $datePickerMode eq 'inline'}
          onSelect: loadUrl,
          {/if}
          defaultDate: defaultDate,      // set the date to highlight on first opening
          altFormat: 'yy-mm-dd',         // set date format which is sent behind the scenes
          altField: '#actualDate',       // set the form field which contains the alternate date format
          beforeShowDay: addDates,       // callback function
          showOn: "both",                // show calender only when button is pressed
          buttonImage: "{$buttonImage}", // path to calendar icon
          buttonImageOnly: "true",       // no button, only the image
          buttonText: "",                // no button text
          changeMonth: {$changeMonth},   // drop down menu for months
          changeYear: {$changeYear},     // drop down menu for years
      {rdelim};

      // set date picker defaults
      $.datepicker.setDefaults($.extend({ldelim}showMonthAfterYear: false{rdelim},
                                        $.datepicker.regional[""],
                                        pickerOpts));


      // Create date picker
      $("#datePicker").datepicker($.datepicker.regional["de"]);
      $("#locale").change(function() {ldelim}
                              $("#datePicker").datepicker("option",
                                                          $.extend({ldelim}showMonthAfterYear: false{rdelim},
                                                          $.datepicker.regional[$(this).val()]));
                          {rdelim});

      // Add event dates for datepicker
      function addDates(date){ldelim}

          // Check each day in event array.
          for (x = 0; x < days.length; x++) {ldelim}
              // If date is in event database...
              if (date.getFullYear() == years[x] &&
                  date.getMonth() == months[x] - 1 &&
                  date.getDate() == days[x]) {ldelim}
                  // ...make day selectable.
                  return [true, "eventDate_class"];
              {rdelim}
          {rdelim}
          // Other days are unselectable.
          return [false, ''];
      {rdelim}

      {if $datePickerMode eq 'inline'}
      function loadUrl(date, instance){ldelim}
          document.datePickerForm.submit();
          {rdelim}
      {/if}
  {rdelim});
</script>

<form id="datePickerForm" name="datePickerForm" method="post" action="{url parameter=$currentPage additionalParams='&%1$s[action]=submit'|vsprintf:$prefixId setup='lib.tx_ptlist.typolinks.datePicker}">
  <input type="hidden" name="{$prefixId}[date]" id="actualDate" />

{if $datePickerMode eq 'overlay'}
  <input type="text" id="datePicker" />&nbsp;
  <br /><br />
  <input type="submit" value="{$submitLabel}" />
{elseif $datePickerMode eq 'inline'}
  <div id="datePicker"></div>
{else}
  <div id="datePicker"></div>
{/if}

</form>
