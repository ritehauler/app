$('#fixed-dp').datepicker({onSelect: function (fd){
        if(fd){setTableDate(fd,"next")}

     //   console.log('selection',fd);
    }})

var thisElement = null;

$( document ).on( "mousemove", function( event ) {
    $( "#log" ).text( "pageX: " + event.pageX + ", pageY: " + event.pageY );
});


$(".modal-current-position").click(function(){
    thisElement = this;

    $(this).addClass("boxHighlight");
    $(this).text("Add new event");

    var position  = $(this).position();
    //$(this).text( "left: " + position.left + ", top: " + position.top );


    if(position.left > 650){position.left = position.left - 645;}
    if(position.top > 350 && position.top < 1500){position.top = position.top - 200;}


    $('#setEventDialog').css({"top": 0, "left": 0,"float": "left"});//Reset draggable position
    $('.setEventCustomModal').css({"top": position.top+50, "left": position.left+160,"float": "left"});//Set modal position as mouse position
    $('.setEventCustomModalContainer').css({'display' : 'block'});
});

$(".closeModal").click(function(){
    $(thisElement).removeClass("boxHighlight");
    $(thisElement).removeClass("boxHighlightSaved");
    $(thisElement).text("");
    $('.setEventCustomModalContainer').css({'display' : 'none'});
});

$(".saveEvent").click(function(){
    $(thisElement).removeClass("boxHighlight");
    $(thisElement).addClass("boxHighlightSaved");
    $('.setEventCustomModalContainer').css({'display' : 'none'});
});

//Drag Modal by header
$("#setEventDialog").draggable({handle: ".modal-header"});
$(".setEventCustomModal").draggable({handle: ".setEventCustomModalHeader"});















const days = ['Sun', 'Mon', 'Tue', 'Wed', 'Thu', 'Fri', 'Sat'];
const monthNames = ["Jan", "Feb", "Mar", "Apr", "May", "Jun", "Jul", "Aug", "Sep", "Oct", "Nov", "Dec"];
const monthNamesFull = ['January', 'February', 'March', 'April', 'May', 'June', 'July', 'August', 'September', 'October', 'November', 'December'];

function nextAndPreviousDate(date,day,action){

    //console.log('yes'+date);
    //var date = Date.parse(date.replace('-', '/').replace('-', '/'));
    var tomorrow = new Date(date);
    var d = null;
    if(action == 'add'){
        d = tomorrow.getDate()+day;
    }else{
        d = tomorrow.getDate()-day;
    }
    var k = new Date(tomorrow.setDate(d));
    k = k.getDate() +"-"+ monthNames[k.getMonth()] +"-"+ k.getFullYear();
    return k;
}//End function

function addDayAndMonth(theDate, days) {
    var responseArray = {};
    var k = new Date(theDate.getTime() + days*24*60*60*1000);
    responseArray.day = k.getDate();
    responseArray.month = k.getMonth();
    responseArray.year = k.getFullYear();
    return responseArray;
}

function createDayIncrement(dateObj){
    var dateArray = [];
    for(var i = 0; i <= 6; i++){
        var getDay = addDayAndMonth(dateObj, i)
        dateArray.push({
            day :  getDay.day,
            month : getDay.month+1,
            year : getDay.year,
            dayName : days[dateObj.getDay() + i]
        });
    }//End for loop
    return dateArray
}

function nextAndPreviousWeek(currentDate,keyword){
   // console.log('cc',currentDate);

    var resObj = {};
    var tempObj = {};
    var currentDate = Date.parse(currentDate.replace('-', '/').replace('-', '/'));
    var curr = new Date(currentDate); // get current date

  //  console.log('current',curr);
    var first_n = curr.getDate() - curr.getDay(); // First day is the day of the month - the day of the week
    // console.log(curr.setDate(first_n));
    var last_n = first_n + 6; // last day is the first day + 6
    tempObj.firstday_n = new Date(curr.setDate(first_n));
    tempObj.lastday_n = new Date(curr.setDate(last_n));

   // console.log('nexttt', tempObj.lastday_n);
    //Getting next and previous date
    resObj.prevDate = nextAndPreviousDate(currentDate,7,"less");

    expDate = addDays(currentDate);
    var nextDateTemp = expDate.getDate() +"-"+ monthNames[expDate.getMonth()] +"-"+expDate.getFullYear();
    var nextDateT = Date.parse(nextDateTemp.replace('-', '/').replace('-', '/'));
    resObj.nextDate = nextAndPreviousDate(nextDateT,1,"add");

    //Creating week dates array
    resObj.dateArray = createDayIncrement(tempObj.firstday_n);

    var preMonth = monthNames[resObj.dateArray[0].month-1];
    var nexMonth = monthNames[resObj.dateArray[resObj.dateArray.length-1].month-1];
    var year = resObj.dateArray[resObj.dateArray.length-1].year;

    var month = null;
    if(preMonth === nexMonth){
        month = monthNamesFull[resObj.dateArray[0].month-1]+" "+year;
    }else{
        month = preMonth +" - "+ nexMonth +" "+year;
    }//End if condition

    resObj.month = month;
    console.log(resObj);
    return resObj;
}//End fucntion


function addDays(formDate)
{
    var current = new Date(formDate);     // get current date
    var weekstart = current.getDate() - current.getDay() +1;
    var weekend = weekstart + 6;       // end day is the first day + 6
    var monday = new Date(current.setDate(weekstart));
    var sunday = new Date(current.setDate(weekend));
    return sunday;
}

var nDate = null;
var pDate = null;
function setTableDate(date,keyword){
    var resObj = {};
    var dateArray = nextAndPreviousWeek(date,keyword);
    nDate = dateArray.nextDate;
    pDate = dateArray.prevDate;
    console.log(dateArray);


    $('#start_date').val('');
    $('#end_date').val('');
    $('#end_date').val(dateArray.nextDate);

    document.getElementById('dateTableHeading').innerHTML = "\
        <td width='4%'><span class='dateHeading'><span class='gmtBox'>GMT</span></span></td>\
        <td width='13.5%'>"+dateArray.dateArray[0].dayName+" <span class='dateHeading'>"+dateArray.dateArray[0].day+"</span></td>\
        <td width='13.554%'>"+dateArray.dateArray[1].dayName+" <span class='dateHeading'>"+dateArray.dateArray[1].day+"</span></td>\
        <td width='13.654%'>"+dateArray.dateArray[2].dayName+" <span class='dateHeading'>"+dateArray.dateArray[2].day+"</span></td>\
        <td width='13.65%'>"+dateArray.dateArray[3].dayName+" <span class='dateHeading'>"+dateArray.dateArray[3].day+"</span></td>\
        <td width='13.7%'>"+dateArray.dateArray[4].dayName+" <span class='dateHeading'>"+dateArray.dateArray[4].day+"</span></td>\
        <td width='13.7%'>"+dateArray.dateArray[5].dayName+" <span class='dateHeading'>"+dateArray.dateArray[5].day+"</span></td>\
        <td width='18%'>"+dateArray.dateArray[6].dayName+" <span class='dateHeading'>"+dateArray.dateArray[6].day+"</span></td>\
    ";

    resObj.nDate = nDate;
    resObj.pDate = pDate;

    document.getElementById("month").innerHTML = dateArray.month;

    return resObj;
}//End function



function nextPreviousTableDate(keyword){

    /*$('#start_date').val('');
    $('#end_date').val('');
*/

    console.log("Next - "+nDate);
    console.log("Pre - "+pDate);
    var res = null;
    if(keyword === 'next'){
        // $('#start_date').val(nDate);
        res = setTableDate(nDate,keyword);

    }else{
        //$('#start_date').val(pDate);
        res = setTableDate(pDate,keyword);

    }//End if condition
    nDate = res.nDate;
    pDate = res.pDate;

    getBusyOrderTime($('#driver_id').val());

}

//Getting current date for first time
var dd = currenDate = new Date();
currenDate = currenDate.getDate() +"-"+ monthNames[currenDate.getMonth()] +"-"+ currenDate.getFullYear();

Date.prototype.GetFirstDayOfWeek = function() {
    var start_week = (new Date(this.setDate(this.getDate() - this.getDay())));
    //console.log(start_week.getMonth());
    var monthNum = parseInt(start_week.getMonth()+1);
    return  start_week.getFullYear()+'-'+monthNum+'-'+start_week.getDate()
}

Date.prototype.GetLastDayOfWeek = function() {
    var end_week = (new Date(this.setDate(this.getDate() - this.getDay() +6)));
    //console.log(end_week);
    var monthNum = parseInt(end_week.getMonth()+1);
    return  end_week.getFullYear()+'-'+monthNum+'-'+end_week.getDate()
}
var today = new Date();
$('#start_date').val(today.GetFirstDayOfWeek());
$('#end_date').val(today.GetLastDayOfWeek());
// console.log(today.GetFirstDayOfWeek());
// console.log(today.GetLastDayOfWeek());
//---------------------------------//

//call function first time
setTableDate(currenDate,"next");

$(document).ready(function(){

      getBusyOrderTime($('#driver_id').val());

    $(document).on('change','#driver_id',function(){
        getBusyOrderTime($(this).val())
    });

    $(document).on('click','.datepicker--cell',function(){
        getBusyOrderTime($('#driver_id').val())
    });




});

