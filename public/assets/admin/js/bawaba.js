
var baseAjaxLink = $('meta[charset="adminlink"]').attr('content');
var baseAjaxLink 
var clipboard = new ClipboardJS('body .copyNum');



    
function employeeChecker(){
    
    var good = true;
    
    if($('#createListForm #tel').val().length > 10  || $('#createListForm #tel').val().length < 9   ){
        good = false;
    }
    
    
    if( ! $('#createListForm .citySelect').val() ) {
        good = false;
    }
    
    $('.productsList .row').each(function(){
         
        var mypr =  $(this).find('.product-q select');
        var pr = mypr.val();
        if(isNaN(pr)){
            good = false;
            mypr.closest('.form-group').addClass('has-error');
        }else {
            mypr.closest('.form-group').removeClass('has-error');
        }
         
        $(this).find('input').each(function() {
             if ($(this).val() == ''){
                 $(this).closest('.form-group').addClass('has-error');
                 good = false;
             }else {
                 $(this).closest('.form-group').removeClass('has-error');
             }
        });
    });
        
    
    return good;
    
}



$('.go_edit_employee').click(function(e){
    
    e.preventDefault();   
    e.stopPropagation();    
    
    var check = employeeChecker();
    
    if(check == false ){
        alert('المرجوا ملأ كل المعلومات وادخال الرقم بشكل صحيح');
        return false;
        
    }
    
    else {
            var id = $(this).attr('data-id');
            var urlAjax = $('#createListForm').attr('action');
            var datastring = $("#createListForm").serialize();
            
            jQuery.ajax({
                url: urlAjax,
                type : 'post',
                data: datastring,
                dataType: "json",
            });
            
            $('#confirm_my_order_please').attr('data-id',id);
            $('#confirm_my_order_please').attr('data-redirect','true');
            $('body #DeliverTimeModal').modal('show');
         
    }
    
    
});










!function(a){a.fn.datepicker.dates.ar={days:["الأحد","الاثنين","الثلاثاء","الأربعاء","الخميس","الجمعة","السبت","الأحد"],daysShort:["أحد","اثنين","ثلاثاء","أربعاء","خميس","جمعة","سبت","أحد"],daysMin:["ح","ن","ث","ع","خ","ج","س","ح"],months:["يناير","فبراير","مارس","أبريل","مايو","يونيو","يوليو","أغسطس","سبتمبر","أكتوبر","نوفمبر","ديسمبر"],monthsShort:["يناير","فبراير","مارس","أبريل","مايو","يونيو","يوليو","أغسطس","سبتمبر","أكتوبر","نوفمبر","ديسمبر"],today:"هذا اليوم",rtl:!0}}(jQuery);


$.extend( $.fn.datepicker.defaults, { language: 'ar'} );

$(document).ready(function(){
     $.fn.datepicker.defaults.language = 'ar';
});



if($('#createListForm').length){
  var city = $('#createListForm .citySelect').attr('data-city');
  $('#createListForm .citySelect').val(city);
}


  


$('#showPaid').click(function(){
  $(this).addClass('activeTab');
  $('#showNotPaid').removeClass('activeTab');
  $('.paied_table').show();
  $('.unpaied_table').hide();
});
$('#showNotPaid').click(function(){
   $(this).addClass('activeTab');
  $('#showPaid').removeClass('activeTab');
  $('.paied_table').hide();
  $('.unpaied_table').show();
});


$("body .danger_me").bind("keyup change", function(e) {
  if($(this).val()){
    $(this).removeClass('danger_me');
  }else{
    $(this).addClass('danger_me');
  }
});

  
var date = new Date();
date.setDate(date.getDate());

/*************** اعدادات اعادة الإتصال ****************/
$('#recall_days').datepicker({
  startDate: date,
  rtl:true
});

$('#recall_days').datepicker({
  startDate: date,
  rtl:true
});




$(document).on('click', '.seemoremony', function() {
   var date    = $(this).data('date');
   var deliver = $(this).data('deliver');
   
   
    $.ajax( {
          type: "POST",
          url: '/lists/cash/',
          data: { date : date , deliver : deliver },
	beforeSend:function(){
                $("#overlay").fadeIn(300);　
        },
          success: function( response ) {
         $("#overlay").fadeOut(0);　

            $('#HistoryModal').modal('show');
            $('#HistoryModal .modal-body').html(response);
            $('body .excelDownloadBtn').attr('href','/download/excel/'+ date +'/'+deliver );
          }
  }); 
});  





$(document).on('click', '.confirmation_btn', function() {
  $('#confirmationForm')[0].submit();
});  




function fill_get_employees_info(){
    $.ajax( {
          type: "GET",
          url: '/loadEmployeesCount',
          dataType: 'json',
          success: function( response ) {
              for (var i=0;i<response.length;++i){
               $('#filler_employee_'+response[i][0]).html(response[i][1]);
              }
          }
      });
}
$('[data-target="#SendOrdersToEmployees"]').click(function(){
  setInterval(function(){ 
    fill_get_employees_info();       
  }, 3000);   
});


/**********************************************/
/************* For Eccommece System ***********/
/**********************************************/

$('body #show_search_box').click(function(){
  $('#search_box').toggle();
});


    


function tooglePassword() {
    var x = document.getElementById("password");
    if (x.type === "password") {
        x.type = "text";
    } else {
        x.type = "password";
    }
}


var today = new Date();
var dd = today.getDate();
var mm = today.getMonth() + 1; //January is 0!

var yyyy = today.getFullYear();
if (dd < 10) {
  dd = '0' + dd;
} 
if (mm < 10) {
  mm = '0' + mm;
} 
var today = dd + '/' + mm + '/' + yyyy;



$( document ).ready(function() {
    $('body #la_date').append(' ' + today);
});




function password_generator( len ) {
    var length = (len)?(len):(10);
    var string = "abcdefghijklmnopqrstuvwxyz"; //to upper 
    var numeric = '0123456789';
    var punctuation = '!@#$%^&*()_+~`|}{[]\:;?><,./-=';
    var password = "";
    var character = "";
    var crunch = true;
    while( password.length<length ) {
        entity1 = Math.ceil(string.length * Math.random()*Math.random());
        entity2 = Math.ceil(numeric.length * Math.random()*Math.random());
        entity3 = Math.ceil(punctuation.length * Math.random()*Math.random());
        hold = string.charAt( entity1 );
        hold = (password.length%2==0)?(hold.toUpperCase()):(hold);
        character += hold;
        character += numeric.charAt( entity2 );
        character += punctuation.charAt( entity3 );
        password = character;
    }
    password=password.split('').sort(function(){return 0.5-Math.random()}).join('');
    $("#password").val(password.substr(0,len));
}



$('.limitPerPage_dropdown a').click(function(e){
  e.preventDefault();
  var limit = $(this).attr('href');
  var url = new URL(document.URL);

  if(window.location.href.indexOf('limit=') > 0){
      url.searchParams.set('limit', limit);
  }else{
      url.searchParams.append('limit', limit); 
  }
  
  window.location.replace(url);
});





$('#uploadSheetModal').on('hidden.bs.modal', function () {
    $(this).find('.alert').each(function(index, el) {
      $(this).remove();
    });
});


$('#setOrdersToProduct').on('hidden.bs.modal', function () {
    $(this).find('.alert').each(function(index, el) {
      $(this).remove();
    });
});

$('#CitiesModal').on('hidden.bs.modal', function () {
    $(this).find('.alert').each(function(index, el) {
      $(this).remove();
    });
});


$('#SendOrdersToEmployees').on('hidden.bs.modal', function () {
    $(this).find('.alert').each(function(index, el) {
      $(this).remove();
    });
});

$('#deleteOrdersModal').on('hidden.bs.modal', function () {
    $(this).find('.alert').each(function(index, el) {
      $(this).remove();
    });
});




/****** GET THE IDS ****/ 
$('#send_to_girls_btn').click(function(e){
  e.preventDefault();
   var employeesForm = $('#SendOrdersToEmployees .modal-body form');
  if(!$('#AssignToEmployeeIDS').val()){
    var alertempty = '<div class="alert alert-danger">المرجوا اختيار الطلبات أولا </div>';
    if($('#SendOrdersToEmployees .modal-body form .alert').length){
        return false;
    }else{
    employeesForm.prepend(alertempty);
    }
    return false;
  }
  $('#SendOrdersToEmployees .alert').hide();

  var alertError = '<div class="alert alert-danger">لا يمكن ترك جميع الحقول فارغة</div>';
  var employeesForm = $('#SendOrdersToEmployees .modal-body form');
  var send = false;
  $("body .countSendForEmployee").each(function(){
      if($(this).val()){
        send = true;
      }
  });

  var numbern = parseInt($('.debo').text(), 10);
  if(numbern < 0) {
  var  msg = 'لم يتم تحديد هذه الكمية من الطلبات ، او ليس موجودة';
  var  alertWrapper = '<div class="alert alert-danger"><strong>تنبيه!</strong> '+ msg +' <</div>'
  $('#SendOrdersToEmployees form').prepend(alertWrapper);
  return false;
  }

   if(send == false){
      employeesForm.prepend(alertError);
    return false;
   }else{
       $('#SendOrdersToEmployees .alert').hide();
       $('#send_to_girls_btn').prop('disabled', true);
       employeesForm.submit();
   }
   return false;
});





$('#transform_form').submit(function(event) {
  var submit = true;
  event.preventDefault();

  $(this).find('.alert').remove();
  $(this).find('select').each(function(){
    if($(this).val() == 'aucun'){
      submit = false;
    }
  });

  if(submit == false) {
    var alertempty = '<div class="alert alert-danger">المرجوا اختيار الموظفات أولا </div>';
    $('#transform_form').prepend(alertempty);
    return false;
  }

  var from = $('[name="from"]').val();
  var to   = $('[name="to"]').val();

  if(from == to ){
    submit = false;
  }

  if(submit == false) {
    var alertempty = '<div class="alert alert-danger">لا يمكن تحويل الطلبات من وإلى نفس الموظفة ، المرجوا اختيار موظفتين مختلفتين</div>';
    $('#transform_form').prepend(alertempty);
    return false;
  }

  if(submit == true){
    $('[type="submit"]').prop('disabled',true);
    $(this)[0].submit();
  }
  return false;
});



$('#addCityForm').submit(function(event) {
  var submit = true;
  event.preventDefault();
  $(this).find('select').each(function(){
    if($(this).val() == 'aucun'){
      submit = false;
    }
  });

  if(submit == false) {
    var alertempty = '<div class="alert alert-danger">  المرجوا اختيار الموزع أولا    </div>';
    if($('#addCityForm .alert').length){
                return false;
    }else{
        $('#addCityForm').prepend(alertempty);
    }
    return false;
  }
  $(this).find('[type="submit"]').prop('disabled',true);
  $(this)[0].submit();
});





$('#deleteOrdersModal form').submit(function(e){
  e.preventDefault();
  if(!$('#AssignToDelete').val()){
    var alertempty = '<div class="alert alert-danger">المرجوا اختيار الطلبات أولا </div>';
    
    if($('#deleteOrdersModal .modal-body form .alert').length){
        return false;
    }else{
        $('#deleteOrdersModal form').prepend(alertempty);
    }
    return false;
  }
  $(this)[0].submit();
});



$('#RestoreNewOrdersModal form').submit(function(e){
  e.preventDefault();
  if(!$('#AssignToRestore').val()){
    var alertempty = '<div class="alert alert-danger">المرجوا اختيار الطلبات أولا </div>';
    $('#RestoreNewOrdersModal form').prepend(alertempty);
    return false;
  }
  $(this)[0].submit();
});



$('#removeOrdersModal form').submit(function(e){
  e.preventDefault();
  if(!$('#AssignToRemove').val()){
    var alertempty = '<div class="alert alert-danger">المرجوا اختيار الطلبات أولا </div>';
    $('#removeOrdersModal form').prepend(alertempty);
    return false;
  }
  $(this)[0].submit();
});









$('#RestoreFromDuplicated form').submit(function(e){
  e.preventDefault();
  if(!$('#RestoreFromDuplicates').val()){
    var alertempty = '<div class="alert alert-danger">المرجوا اختيار الطلبات أولا </div>';
    $('#RestoreFromDuplicated form').prepend(alertempty);
    return false;
  }
  $(this)[0].submit();
});






$('#uploadSheetModal form').submit(function(e){
  e.preventDefault();
    var alertempty = '<div class="alert alert-danger">  المرجوا اختيار الملف أولا </div>';
    if ($('#upload_sheet_input').get(0).files.length === 0) {
       if($('#uploadSheetModal .modal-body form .alert').length){
            return false;
        }else{
            $('#uploadSheetModal form').prepend(alertempty);
        }
        return false;
    } 
  $('#uploadSheetModal form .alert').remove();
  $(this)[0].submit();
});





$('#assing_to_citie').click(function(e){
  e.preventDefault();
  if(!$('#AssignToCityIDS').val()){
    var alertempty = '<div class="alert alert-danger">المرجوا اختيار الطلبات أولا </div>';

    if($('#CitiesModal .modal-body form .alert').length){
        return false;
    }else{
        $('#CitiesModal form').prepend(alertempty);
    }

    return false;
  }
  $('#CitiesModal form .alert').hide();

    var city = $('#citiesSelect').val();
    var alertError = '<div class="alert alert-danger">المرجوا اختيار المدينة أولا</div>';
    if( city == 'N-A') {
      $('#assign-cities-alert').html(alertError);
      return false;
    }
  $(this).closest('form').submit();
});

$('#assing_to_product').click(function(e){
  
  e.preventDefault();
  if(!$('#AssignToProductIDS').val()){
    var alertempty = '<div class="alert alert-danger">المرجوا اختيار الطلبات أولا </div>';

    if($('#setOrdersToProduct .modal-body form .alert').length){
        return false;
    }else{
        $('#setOrdersToProduct form').prepend(alertempty);
    }
    return false;
  }

  $('#CitiesModal form .alert').hide();
  var city = $('#ProductsSelect').val();
  var alertError = '<div class="alert alert-danger">المرجوا اختيار المنتوج أولا</div>'
  if( city == 'N-A') {
    $('#assign-product-alert').html(alertError);
    return false;
  }else {
    $(this).closest('form').submit();
  }
});






$(document).on('keyup', '#SendOrdersToEmployees .countSendForEmployee', function(evt) { 
   calculateSumForGirls();
});


// edit list page , set the value to ordere list
var $myDiv = $('.editMultsaleList');
if ( $myDiv.length){
   $('.editMultsaleList select').each(function(){
       var productID = $(this).data('product');
       $(this).val(productID);
   });
}
    
var editcity = $('#editcity').data('cityid');
$('#editcity').val(editcity);
 

var editOrderEmployee = $('#editorderemployee');
if(editOrderEmployee.length){
  var employee =  editOrderEmployee.data('employeeid');
  editOrderEmployee.val(employee);
}




$('a#see_reason').click(function(){
    var reason = $(this).data('reason');
    $('.reason_here').html(reason);
});


$('[data-target="#DeliverTimeModal"]').click(function(){
     $('#myModal').modal('hide'); 
});

function calculateSumForGirls() {
    var old = parseInt($('span.debo').data('girlstotal'), 10);
    var sum = 0;
    $("#SendOrdersToEmployees .countSendForEmployee").each(function() {
        if ($(this).val()) {
            sum += parseInt($(this).val(), 10);
        }
    });
    $('body span.debo').html(old-sum);
} 

$('[data-target="#SendOrdersToEmployees"]').click(function(){
    if( $('#selectedRows').val()){
        var y = $('#selectedRows').val().split(",");
        $('span.debo').html(y.length);
        $("span.debo").attr('data-girlsTotal', y.length);
    }
});

 
        
        
function calculateSum() {
    var old = parseInt($('.ExistNumber').data('num'), 10);
    var sum = 0;
    //iterate through each textboxes and add the values
    $("#leeetch [type='number']").each(function() {
        //add only if the value is number
        if (!isNaN(this.value) && this.value.length != 0) {
            sum += parseFloat(this.value);
        }
    });
    $('.ExistNumber').html(old-sum);
}

$('#SendOrdersToEmployees').on('hidden.bs.modal', function () {
  $(this).find('input').each(function(){
      $(this).val('');
  });
})






// Go back button in admin pages header
$(document).ready(function(){
  $('.goback').click(function(){
    parent.history.back();
    return false;
  });
});


function go_full_screen(){
  var elem = document.documentElement;
  if (elem.requestFullscreen) {
    elem.requestFullscreen();
  } else if (elem.msRequestFullscreen) {
    elem.msRequestFullscreen();
  } else if (elem.mozRequestFullScreen) {
    elem.mozRequestFullScreen();
  } else if (elem.webkitRequestFullscreen) {
    elem.webkitRequestFullscreen();
  }
}



if($.cookie("colapse") == 'ok') {
    $('body').addClass('sidebar-xs');
}
$('.sidebar-main-toggle').on('click', function(){

    if($.cookie("colapse") == 'ok') {
        $.removeCookie('colapse', { path: '/' });
        return false;
    } else {
        $.cookie('colapse', 'ok' ,{ expires: 7, path : '/'});
    }
});



$('.order_dropdown a').click(function(e){
  e.preventDefault();
  var order = $(this).attr('href');
  var url = new URL(document.URL);

  if(window.location.href.indexOf('order=') > 0){
      url.searchParams.set('order', order);
  }else{
      url.searchParams.append('order', order); 
  }
  

  if(window.location.href.indexOf('page=') > 0){
      url.searchParams.set('page', 1);
  }else{
      url.searchParams.append('page', 1); 
  }
  

  window.location.replace(url);
});



$('body .data-sort').click(function(){
  $(this).removeClass('icon-arrow-up5');
  $(this).addClass('icon-arrow-down5');
  var url = new URL(document.URL);
  var sort = $(this).attr('data-sort');
   if(window.location.href.indexOf('sort=') > 0){
      url.searchParams.set('sort', sort);
  }else{
      url.searchParams.append('sort', sort); 
  }
  if(window.location.href.indexOf('page=') > 0){
      url.searchParams.set('page', 1);
  }else{
      url.searchParams.append('page', 1); 
  }
  window.location.replace(url);
});





$('#limitPerPage').change(function(e){
  e.preventDefault();
  var limit = $(this).val();
  var url = new URL(document.URL);
  
  if(window.location.href.indexOf('limit=') > 0){
      url.searchParams.set('limit', limit);
  }else{
      url.searchParams.append('limit', limit); 
  }

  if(window.location.href.indexOf('page=') > 0){
      url.searchParams.set('page', 1);
  }else{
      url.searchParams.append('page', 1); 
  }
  

  
  window.location.replace(url);
});


$('.employee_dropdown a').click(function(e){
  e.preventDefault();
  var employee = $(this).attr('href');
  var url = new URL(document.URL);

  if(window.location.href.indexOf('employee=') > 0){
      url.searchParams.set('employee', employee);
  }else{
      url.searchParams.append('employee', employee); 
  }

  if(window.location.href.indexOf('page=') > 0){
      url.searchParams.set('page', 1);
  }else{
      url.searchParams.append('page', 1); 
  }
  

  
  window.location.replace(url);
});


$('.product_dropdown a').click(function(e){
  e.preventDefault();
  var product = $(this).attr('href');
  var url = new URL(document.URL);

  if(window.location.href.indexOf('product=') > 0){
      url.searchParams.set('product', product);
  }else{
      url.searchParams.append('product', product); 
  }
  
  if(window.location.href.indexOf('page=') > 0){
      url.searchParams.set('page', 1);
  }else{
      url.searchParams.append('page', 1); 
  }
  



  window.location.replace(url);
});








$('.day_dropdown a').click(function(e){
  e.preventDefault();
  var product = $(this).attr('href');
  var url = new URL(document.URL);

  if(window.location.href.indexOf('day=') > 0){
      url.searchParams.set('day', product);
  }else{
      url.searchParams.append('day', product); 
  }
  window.location.replace(url);
});









$('.charges_limit a').click(function(e){
  e.preventDefault();
  var limit = $(this).attr('href');
  var url = new URL(document.URL);
  
  if(window.location.href.indexOf('limit=') > 0){
      url.searchParams.set('limit', limit);
  }else{
      url.searchParams.append('limit', limit); 
  }
  
  if(window.location.href.indexOf('page=') > 0){
      url.searchParams.set('page', 1);
  }else{
      url.searchParams.append('page', 1); 
  }

  window.location.replace(url);
});


$('.charges_since a').click(function(e){
  e.preventDefault();
  var from = $(this).attr('href');
  var url = new URL(document.URL);
  if(window.location.href.indexOf('from=') > 0){
      url.searchParams.set('from', from);
  }else{
      url.searchParams.append('from', from); 
  }


  if(window.location.href.indexOf('page=') > 0){
      url.searchParams.set('page', 1);
  }else{
      url.searchParams.append('page', 1); 
  }


  window.location.replace(url);
});


$('.charges_paied a').click(function(e){
  e.preventDefault();
  var paied = $(this).attr('href');
  var url = new URL(document.URL);
  
  if(window.location.href.indexOf('paied=') > 0){
      url.searchParams.set('paied', paied);
  }else{
      url.searchParams.append('paied', paied); 
  }
  
  if(window.location.href.indexOf('page=') > 0){
      url.searchParams.set('page', 1);
  }else{
      url.searchParams.append('page', 1); 
  }
  


  window.location.replace(url);
});



$('.charges_type a').click(function(e){
  e.preventDefault();
  var type = $(this).attr('href');
  var url = new URL(document.URL);
  
  if(window.location.href.indexOf('type=') > 0){
      url.searchParams.set('type', type);
  }else{
      url.searchParams.append('type', type); 
  }

  if(window.location.href.indexOf('page=') > 0){
      url.searchParams.set('page', 1);
  }else{
      url.searchParams.append('page', 1); 
  }
  

  
  window.location.replace(url);
});



$('.city_dropdown a').click(function(e){
  e.preventDefault();
  var city = $(this).attr('href');
  var url = new URL(document.URL);
  
  if(window.location.href.indexOf('city=') > 0){
      url.searchParams.set('city', city);
  }else{
      url.searchParams.append('city', city); 
  }

  if(window.location.href.indexOf('page=') > 0){
      url.searchParams.set('page', 1);
  }else{
      url.searchParams.append('page', 1); 
  }
  

  
  window.location.replace(url);
});


$('.type_dropdown a').click(function(e){
  e.preventDefault();
  var type = $(this).attr('href');
  var url = new URL(document.URL);
  
  if(window.location.href.indexOf('type=') > 0){
      url.searchParams.set('type', type);
  }else{
      url.searchParams.append('type', type); 
  }

  if(window.location.href.indexOf('page=') > 0){
      url.searchParams.set('page', 1);
  }else{
      url.searchParams.append('page', 1); 
  }
  

  
  window.location.replace(url);
});


$('.deliver_dropdown a').click(function(e){
  e.preventDefault();
  var deliver = $(this).attr('href');
  var url = new URL(document.URL);
  
  if(window.location.href.indexOf('deliver=') > 0){
      url.searchParams.set('deliver', deliver);
  }else{
      url.searchParams.append('deliver', deliver); 
  }

  if(window.location.href.indexOf('page=') > 0){
      url.searchParams.set('page', 1);
  }else{
      url.searchParams.append('page', 1); 
  }
  
  window.location.replace(url);
});





 
$('body .pay_charges').click(function(){
     $(this).prop('disabled',true);
     var id = $(this).data('id');
     $.ajax( {
      type: "POST",
      url: '/charges/paied',
      data: { id : id},
      beforeSend : function(){
        $('.main-loader').show();
      },
      success: function( response ) {
       $('.main-loader').show();
       $(this).remove();
       $("#charges_"+id + " td:last-child").html(response);
      }
    });
});


function show_history(id){
      $.ajax( {
        type: "POST",
        url: '/loadHistory',
        data: { id : id},
        success: function( response ) {
          $('#HistoryModal').modal('show');
          $('#HistoryModal .modal-body').html('<ul></ul>');
          $.each(JSON.parse(response), function (k,v) {
            $('#HistoryModal ul').append('<li>'+ v +'</li>');
          });
        }
      });
      return false;
}


$("body .show_history").click(function(){
     var id = $(this).data('id');
     $.ajax( {
      type: "POST",
      url: '/loadHistory',
      data: { id : id},
beforeSend:function(){
    $("#overlay").fadeIn(300);　
},
      success: function( response ) {
$("#overlay").fadeOut(0);
        $('#HistoryModal').modal('show');
        $('#HistoryModal .modal-body').html('<ul></ul>');
        $.each(JSON.parse(response), function (k,v) {
          $('#HistoryModal ul').append('<li>'+ v +'</li>');
        });
      }
    });
});



    
    function loaddata(listID,AjaxLink,type){
       $.ajax( {
          type: "POST",
          url: AjaxLink,
          data: { list_id : listID , type : type },
        beforeSend:function(){
    $("#overlay").fadeIn(300);　
},

          success: function( response ) {
                $("#overlay").fadeOut(0);　
                  $('#loadData').modal('show');
                  $('#loadData').find('.modal-body').html(response);
          },
          error : function (){
            alert('حصل خطأ غير متوقع ، المرجوا المحاولة من جديد');
          }
          });  
    }      
    
    /******* تحميل المعلومات في صفحة كل الطلبات ***********/
    $('body .loaddata').click(function(){
        var listID = $(this).data('listid');
        var AjaxLink = '/lists/'+listID ; 
        loaddata(listID,AjaxLink); 
    });
    
    /******** تحميل المعلومات عند الموظفات **********/
    $('body .loadactions').click(function(){
        var listID = $(this).data('listid');
        var AjaxLink =  '/lists/loadAction/'+listID;
        loaddata(listID,AjaxLink,'employee');
    });
    
    /*********** تحميل المعلومات عند الموزعين **********/
    $('body .loadDeliveractions').click(function(){
        var listID = $(this).data('listid');
        var AjaxLink =  '/lists/loadAction/'+listID;
        loaddata(listID,AjaxLink,'deliver');
    });
    
  
    $('body #recall_houres').keyup(function(){
        if($(this).val() != ''){
            $('.recall_days_container').hide();
        }else {
            $('.recall_days_container').show();
        }
    });

    $('body #recall_days').change(function(){
        if($(this).val() != ''){
             $('body #recall_days').attr("readonly", false); 
            $('.recall_houres_container').hide();
        }else {
             $('body #recall_days').attr("readonly", true); 
            $('.recall_houres_container').show();
        }
    });
    

    
    
    /************* المدير يتحكم في الطلب عند الموظفة والموزع ****************/
    $(document).on('click', '.adminActionBtn', function() {


            var type   = $(this).data('type');
            var ListID = $(this).data('listid');
            var role   = $(this).data('role');
            var edit   = $(this).data('edit');


            if(type == 'restoreSuivi') {
                $('#loadData').modal('hide');
                SetReStoredSuivi(ListID);
            }

            if(type == 'Canceled') {
                $('#loadData').modal('hide');
                $('body #the_cancel_reason').attr('data-listid',ListID);
                $('body #the_cancel_reason').attr('data-type',role);
                $("#adminEmployeeCancel").modal('show');
            }
            if(type == 'confirmed') {
                   SentOrderConfirmed(ListID,role,edit); 
            }
            if(type == 'NoResponse') {
                   NoResponsByAdmin(ListID,role); 
            }

            if(type == 'Recall') {
                   $('#loadData').modal('hide');
                   $("#AdminRecallModal").modal('show');
                   $('body #make_sure_recall').attr('data-listid',ListID);
                   $('body #make_sure_recall').attr('data-role',role);
            }

     });


   /************* المدير لا يجيب عند الموزع والموظفة ********************/
   function NoResponsByAdmin(ListID,role){

        if(role == 'employee') {
            var urlAjax = '/lists/setNoResponse/'; 
        }

        if(role == 'deliver') {
            var urlAjax = '/sentlists/setNoResponse/'; 
        }

        jQuery.ajax({
            url: urlAjax,
            type : 'post',
            data : {'get_id' : ListID },
	
            success : function ( response ) {
               $('body #loadData').modal('hide');
               if(response === 'send_sms'){
                  $('#sendSMS').modal('show');
               } 
               $('body .row_'+ListID).remove();
            },
        }); 
    }



    function SetReStoredSuivi(ListID){
        var urlAjax = '/lists/reset'; 
        jQuery.ajax({
            url: urlAjax,
            type : 'post',
            data : {'list' : ListID },
            success : function ( response ) {
               console.log(response);
               $('body .row_'+ListID).remove();
            },
        }); 
    }





$('body #confirm_my_order_please').click(function(){
  
      var houres = $('#DeliverTimeModal  #recall_houres');
      var days   = $('#DeliverTimeModal  #recall_days');
      var list   = $(this).attr('data-id');
      var redirect   = $(this).attr('data-redirect');
      
      if(!houres.val() && !days.val() ) {
          return false;
      }

      else {
            
            //  $('#confirm_my_order_please').prop( "disabled", true );

              h = houres.val();
              d = days.val();
      
              jQuery.ajax({
                  url: '/lists/setSentEmployee',
                  type : 'POST',
                  data : { list :  list , houres :h , days : d },
                      beforeSend  : function() {
                        
                      },
                      success : function ( response ) {  
                          if(response == 'NEDDED_INFO'){
                              $('#DeliverTimeModal').modal('hide');
                              $('#InfoNeeded').modal('show');
                              return false;
                          }else {
                              if(redirect){
                                window.location.replace("/");
                              }

                              $('body .row_'+list).remove();
                              $('body #recall_houres').val('');
                              $('body #recall_days').val('');
                              $('body #DeliverTimeModal').modal('hide');
                              $('body #confirm_my_order_please').prop( "disabled", false );                                                         
                              $('body #confirm_my_order_please').removeAttr("disabled");  
                          }                                                   
                      },
              });
              
    }
    
    return false;
    
});


$('#DeliverTimeModal').on('hidden.bs.modal', function () {
        $('.recall_houres_container').show();
        $('.recall_days_container').show();
        $('body #recall_houres').val('');
        $('body #recall_days').val('');
        $('body #confirm_my_order_please').prop( "disabled", false );                                                         
        $('body #confirm_my_order_please').removeAttr("disabled");  
});



  
    /************ المدير قام بقبول الطلب عند الموظفة و الموزع *********/
    function SentOrderConfirmed(ListID,role,edit){


            if(role == 'employee') {
              if(edit){
                var link = baseAjaxLink + "/lists/edit/"+ ListID +"/?returnURI=(" + baseAjaxLink + "/lists?view=lists&type=waiting)";
                $('#loadData').modal('hide');
                $('#InfoNeeded').modal('show');
                $('#InfoNeeded .editBtn').attr('href',link);
                return false;
              }else {
                $('#loadData').modal('hide');
                $('.recall_houres_container').show();
                $('.recall_days_container').show();
                $('#DeliverTimeModal').modal('show');
                $('#confirm_my_order_please').attr('data-id',ListID);
              }
               
                return false;
            }


            if(role == 'deliver') {
                var urlAjax = '/sentlists/setDelivred'; 
                 jQuery.ajax({
                url: urlAjax,
                type : 'POST',
                data : { list :  ListID},
                success : function ( response ) {    
                    if(response == 'NEDDED_INFO'){
                      $('#loadData').modal('hide');
                      $('#InfoNeeded').modal('show');
                      return false;
                    }

                    if(response === 'please_fill_info') {
                      var message_alert = 'من فضلك املأ الخانات الفارغة';
                      return  alert(message_alert);
                    }
                    
                    else {
                       $('body .row_'+ListID).remove();
                       $('#loadData').modal('hide');
                    }
                },
            });
            }
            

           
      }

    
        /************ المدير قام بالغاء الطلب عند الموظفة والموزع *********/
        $(document).on('click', '#send_reason', function() {
            
            var CancelListID =  $('#the_cancel_reason').attr('data-listid');
            var type = $('body #the_cancel_reason').data('type');
            var reason = $('#the_cancel_reason').val();
        
            if (reason == ''){
                alert('المرجوا اضافة سبب الإلغاء أولا');
                return false;
            }

            var urlAjax = '/lists/setCanceled/'; 
           
            jQuery.ajax({
                url: urlAjax ,
                type : 'post',
                data : {'list_id': CancelListID , 'reason' :  reason },
                beforeSend : function (){
                    $('#send_reason').attr('disabled','true');
                    $('#send_reason').text('جاري تعيين ك ملغية ، المرجوا الإنتظار قليلاً');
                },
                success : function ( response ) {        
                    $('body .row_'+CancelListID).remove();
                    $('#adminEmployeeCancel').modal('hide');
                    $('body #the_cancel_reason').val('');
                    $('#send_reason').remove();
                
                },
                error : function( response ){
                    alert('حصل خطأ غير متوقع المرجوا المحاولة من جديد');
                },
                complete : function( response){
                $('#adminEmployeeCancel .modal-footer').append('<a type="button" id="send_reason" class="btn btn-primary">الغاء وارسال السبب</a>');
                $('body').attr('style','padding-right: 0px !important; '); 

                }
        });
    });
    
    
    
    
    /************ المدير قام بإعادة الإتصال للطلب عند الموظفة و الموزع  *********/
    $('body #make_sure_recall').click(function(){
        var ListID =  $(this).attr('data-listid');
       // alert(ListID);
        var recall_houres = $('#AdminRecallModal #recall_houres').val();
        var recall_days = $('#AdminRecallModal #recall_days').val();
        var role = $(this).data('role');
        
        if(role == 'employee') {
            var urlAjax = '/lists/setRecall/'; 
        }

        if(role == 'deliver') {
            var urlAjax = '/sentlists/setRecall/'; 
        }                    
        
        if(recall_houres.length == 0 && recall_days.length == 0) {
                     swal({
                          title: "خطأ",   
                          text: "المرجوا ادخال توقيت اعادة الإتصال",   
                          type: "warning" 
                    });   
            return false;
        }
        
        if(recall_houres){ var recall_type = 'houres'}
        if(recall_days){ var recall_type = 'days'}
        
        $('body .row_'+ListID).remove();
        
         var formdata   = new FormData();
         formdata.append('list_id',ListID);
         formdata.append('recall_houres',recall_houres);
         formdata.append('recall_days',recall_days);
           
        jQuery.ajax({
            url: urlAjax,
            type : 'post',
            contentType : false,
            processData : false,
            dataType : 'html',
            data: formdata,
            success : function ( response ) {   
                $('#AdminRecallModal').modal('hide');
                $('#recall_houres').val('');
                $('#recall_days').val('');
            } 
        
        });
 
    });
    
    /**************** End modals ************/   
    $('body #see_reason').click(function(){
        var reason = $(this).data('reason');
        $('#loadData').modal('show');
        $('#loadData').find('.modal-body').html('<div class="reasonBox">'+reason+'</div>');
    });
        




    /*************** اعدادات اعادة الإتصال ****************/

    $('body #recall_houres').keyup(function(){
        if($(this).val() != ''){
            $('.recall_days_container').hide();
        }else {
            $('.recall_days_container').show();
        }
    });

    $('body #recall_days').change(function(){
        if($(this).val() != ''){
             $('body #recall_days').attr("readonly", false); 
            $('.recall_houres_container').hide();
        }else {
             $('body #recall_days').attr("readonly", true); 
            $('.recall_houres_container').show();
        }
    });



  $('#AdminRecallModal').on('hidden.bs.modal', function () {
    $('#recall_houres').val('');
    $('#recall_days').val('');
   $('.recall_houres_container').show();
   $('.recall_days_container').show();
  });
    




/****** Check Box Table With Ids System *********/
$(document).ready(function() {
  var $selectAll = $('#checkAll'); // main checkbox inside table thead
  var $table = $('.table'); // table selector 
  var $tdCheckbox = $table.find('tbody .check'); // checboxes inside table body
  var tdCheckboxChecked = 0; // checked checboxes
  

  // Select or deselect all checkboxes depending on main checkbox change
  $selectAll.on('click', function () {
    $tdCheckbox.prop('checked', this.checked);
    getselected();
  });

  // Toggle main checkbox state to checked when all checkboxes inside tbody tag is checked
  $tdCheckbox.on('change', function(e){
    tdCheckboxChecked = $table.find('tbody input:checkbox:checked').length; // Get count of checkboxes that is checked
    // if all checkboxes are checked, then set property of main checkbox to "true", else set to "false"
    $selectAll.prop('checked', (tdCheckboxChecked === $tdCheckbox.length));
    getselected();
  })
  
  function getselected(){
    var $ids  = new Array();
    $(".table tbody .check[type=checkbox]:checked").each(function () {
            $ids.push($(this).data('item'));
    });

      var count = $ids.length;
      if(count > 0 ){
          // do what you want with ids here
          $('.debo').html(' ('+count+') ');
          $('.num_num').each(function(){
            $(this).html(' ('+count+') ');
          });
         $('[id="ids"][name="list"]').val($ids);
          $('#selectedRows').val($ids);
          $('#selectedRows').val($ids);
          $('#AssignToCityIDS').val($ids);
          $('#AssignToProductIDS').val($ids);
          $('#AssignToEmployeeIDS').val($ids);
          $('#AssignToDelete').val($ids);
          $('#AssignToRestore').val($ids);
          $('#RestoreFromDuplicates').val($ids);
          $('#AssignToRemove').val($ids);
          $('#confirmation_ids').val($ids);
          $('[id="ids"][name="list"]').val($ids);
      }else {

          $('.debo').html('');
          $('.num_num').each(function(){
            $(this).html('');
          });
         $('[id="ids"][name="list"]').val($ids);
          $('#selectedRows').val('');
          $('#selectedRows').val('');
          $('#AssignToCityIDS').val('');
          $('#AssignToProductIDS').val('');
          $('#AssignToEmployeeIDS').val('');
          $('#AssignToDelete').val('');
          $('#AssignToRestore').val('');
          $('#RestoreFromDuplicates').val('');
          $('#AssignToRemove').val('');
          $('#confirmation_ids').val('');
          $('[id="ids"][name="list"]').val('');

      }
   
  }
});


/************* CheckBox Table JS **************/    




$('#datePicker , #datepickerFrom , #datepickerTo').datepicker({
    language: "ar",
    rtl: true
});
    
$.fn.datepicker.defaults.format = "mm/dd/yyyy";
$('.datepicker').datepicker({
    language: "ar",
    rtl: true
}); 


var date = new Date();
date.setDate(date.getDate());
$('.datepickerfromtoday').datepicker({
    language: "ar",
    rtl: true,
    startDate: date
}); 





$('#createListForm').submit(function(){
    
  var create = true;


if($('#createListForm #tel').val().length > 10   ||$('#createListForm #tel').val().length < 9   ){
    alert('الرقم غلط ا لالة الشريفة');
    return false;
}
  
  if($('#choseEmployee').val() == 'N-A') {
    alert('المرجوا اختيار الموظفة');
    return false;
  }

 var good = true;
 $('.productsList .row').each(function(){
     
    var mypr =  $(this).find('.product-q select');
    var pr = mypr.val();
    if(isNaN(pr)){
        good = false;
        mypr.closest('.form-group').addClass('has-error');
    }else {
        mypr.closest('.form-group').removeClass('has-error');
    }
     
    $(this).find('input').each(function() {
         if ($(this).val() == ''){
             $(this).closest('.form-group').addClass('has-error');
             good = false;
         }else {
             $(this).closest('.form-group').removeClass('has-error');
         }
    });

 });


 if(good == false) {
  return false;
 }else {
  $(this)[0].submit();
 }
  
 });    





/*********** Charges AJax Starts Here ***********/
$('#chargesFixedFormSave').submit(function(e){
  e.preventDefault();
  var name    = $(this).find('#name').val();
  var amount  =  $(this).find('#value').val();
  if(name && amount) {
      $(this).find('[type="submit"]').prop('disabled',true);
  var urlAjax = '/charges/save';
    jQuery.ajax({
            url: urlAjax,
            type : 'post',
            data : {'name' : name , 'amount': amount },
            success : function ( response ) {
               $('#chargesFixedFormSave').prepend('<div class="alert alert-success">تم اضافة نوع المدفوعات الجديد</div>');
               $('#chargesFixedFormSave').trigger('reset');

               setTimeout(
                function() 
                {
                    location.reload();
                }, 3000);
            },
   }); 
  }
  return false;

});

$('#saveNewCharges').submit(function(e){
  e.preventDefault();
  var name    = $(this).find('#name').val();
  var value  = $(this).find('#value').val();
  var type    = $(this).find('#type').val();
  var urlAjax = $(this).attr('action');

  var  submit = true;
  if(!name || !value){
     submit = false;
     var alertContent = 'المرجوا ادخال المدفوع والقيمة';
     var alertempty = '<div class="alert alert-danger">'+alertContent+'</div>';

    if($('#saveNewCharges .alert').length){
        return false;
    }else{
        $('#saveNewCharges').prepend(alertempty);
    }
  }

  if(submit == true ){
     $(this).find('.alert-danger').remove();
     $(this).find('[type="submit"]').prop('disabled',true);
     jQuery.ajax({
              url: urlAjax,
              type : 'post',
              data : {'name' : name , 'value': value , 'type' : type },
              success : function ( response ) {
                 $('#saveNewCharges').prepend('<div class="alert alert-success">تم تسجيل المدفوعات </div>');
                 $('#saveNewCharges').trigger('reset');
                    setTimeout(
                  function() 
                  {
                      location.reload();
                  }, 2000);
              },
     }); 

  }

   return false;
});


$('#fixed_charges_list select').change(function(){
    var name = $("#fixed_charges_list select option:selected" ).text();
    $('#saveNewCharges .value').val($(this).val());
    $('#saveNewCharges #name').val(name);
});


$("#charges_type").change(function(){
  if($(this).val() == 'fixed'){
    $('#fixed_charges_list').show();
    $('#new_charge_type').hide();
    $('#saveNewCharges #type').val('fixed');
    $('#saveNewCharges .value').val('');
  }else {
    $('#fixed_charges_list').hide();
    $('#new_charge_type').show();
    $('#saveNewCharges #type').val('variable');
    $('#saveNewCharges .value').val('');
  }
});
/*********** Charges AJax Starts Here ***********/






// Add product
$('#addmoreproducts').click(function(){
    
    var row = $(this).closest('.productsTosale');
    var mypr  =  $(this).closest('.row').find('.product-q select');
    var pr = mypr.val();
    
    if(isNaN(pr)){
        mypr.addClass('is-invalid');
    }else {
        mypr.removeClass('is-invalid');
    }
    
    var emptyFound = false;
     $(row).find('input').each(function() {
         if ($(this).val() == ''){
             $(this).closest('.form-group').addClass('has-error');
             emptyFound = true;
         }else {
             $(this).closest('.form-group').removeClass('has-error');
         }
    });

    if(emptyFound){
        return false;
    }
    
    var hheo  =  $('body .productsTosale').first().clone();  
    $(hheo).find('.btn.btn-primary').addClass('btn-danger').html('-').attr("id","removemoreproducts");
    $(hheo).find('input').each(function(){
        $(this).val('');
    })
    $(hheo).appendTo("body .productsList");
});

// Remove Product 
$(document).on('click', '#removemoreproducts', function() {
    $(this).closest('.productsTosale').remove();
});
 
// show the prix de laivraison
$(document).on('change', '[name="cityID"]', function() {
        var city_id = $(this).val();
        
        if(city_id == 'out') {
            $('#outzone_adress').show();
            return false;
        }else {
        $('#outzone_adress').hide();
        $.ajax( {
              type: "POST",
              url: '/lists/deliveryPrice/',
              data: {city_id : city_id  },
              success: function( response ) {
                  $('[name="prix_de_laivraison"]').val(response);
              }
        });
 } 
});
    



 $('body .save_ads').click(function(){
        var day = $(this).attr('data-day');
        $('#SpentModal #daySpent').val(day);
        $('#SpentModal').modal('show');
    });

    $('body #SpentModal #savespent').click(function(){
        var day = $('#SpentModal #daySpent').val();
        var ads = $('#SpentModal #spentFB').val();
        $('#SpentModal').modal('hide');
        var url = '/revenue/spent/'+day+'/'+ads;
        window.location.href = url;
    });
     

    $('body #SpentModalEdit #savespent').click(function(){
        var day = $('#SpentModalEdit #daySpent').val();
        var ads = $('#SpentModalEdit #spentFB').val();
        $('#SpentModal').modal('hide');
        var url = '/revenue/spent/'+day+'/'+ads;
        window.location.href = url;
    });


    var getUrlParameter = function getUrlParameter(sParam) {
        var sPageURL = window.location.search.substring(1),
            sURLVariables = sPageURL.split('&'),
            sParameterName,
            i;
    
        for (i = 0; i < sURLVariables.length; i++) {
            sParameterName = sURLVariables[i].split('=');
    
            if (sParameterName[0] === sParam) {
                return sParameterName[1] === undefined ? true : decodeURIComponent(sParameterName[1]);
            }
        }
    };
    
    $('body #exportSelectedOnly').click(function(){
        var selected = $('#selectedRows').val();
        var type = getUrlParameter('type');

        $('body .selectFormToExport').val(selected);
        $('body .type_selected').val(type);
        
        
        $('#selectFormToExport').submit();
    });


     $('body #exportSelectedOnlyForData').click(function(){
        var selected = $('#selectedRows').val();
        $('body #selectDataFormToExport  .selectFormToExportData').val(selected);
        $('#selectDataFormToExport').submit();
    });

    
    
$(document).on('change', '.entreetable .activate_me', function() {
 
        var id = $(this).data('entree');
        var valid = $('[data-valid="'+id+'"]').val();
        if( valid == ''){
            alert('المرجوا ادخال الرقم ');
            $(this).prop('checked',false);
        }else{
                $.ajax( {
                  type: "POST",
                  url: '/stock/general/validateEntree',
                  data: {id : id , valid : valid },
                  success: function( response ) {
                      
                    $('#item-'+id).remove();
                    alert('تم التفعيل بنجاح');
                  },
                    error: function( response ) {
                      
                    alert('حصل خطا ما');
                  }
                });
        }
    return false;
}); 
    




    $('body .spent').click(function(){
       var day = $(this).attr('data-day');
       var ads = $(this).attr('data-spent');
       // show the modal and data in modal
       $('#SpentModalEdit').modal('show');
       $('#SpentModalEdit #spentFB').val(ads);
       $('#SpentModalEdit #daySpent').val(day);
    });
     


    $('body').off().on('click', '#addRowSortie', function(e) {

        var ddodo =   $('#leeetch .city_quantity').first().clone().appendTo('#leeetch #loadedSortielist');

        $(ddodo).find('[name="quantities[]"]').val('');

        $(ddodo).find('.sortieValidation').removeClass('has-error');

        $('body .city_quantity').each(function(){
            var offdd = $(this).find('.citiesList').val();
            $(ddodo).find('.citiesList [value="'+offdd+'"]').remove();
        });

        var hel = $(ddodo).find('.btn.btn-primary').addClass('btn-danger').html('-').attr("id","RemoveRowSortie");

        $('body #leeetch').append(ddodo);

        e.preventDefault();
    });



    $(document).on('click', '#loadProductsList', function() {
            var sortie_list_id = $(this).data('listproduct');
            var SortieProductID = $(this).data('sortieproductid');
        
            $('#editSortieStock #SortieProductID').val(SortieProductID);
            $('#editSortieStock #SortieListID').val(sortie_list_id);
          
            $.ajax( {
              type: "POST",
              url: '/stock/general/loadSortieLists/',
              data: {id : sortie_list_id  },
              success: function( response ) {
                var loaded =  $('#loadedSortielist');
                  loaded.html(response);
                  loaded.find('select').each(function(){
                      var kk = $(this).data('val');
                      $(this).val(kk);
                  });
              }
            });
            
             $.ajax( {
                  type: "POST",
                  url: '/stock/general/get/rest',
                  data : { 'id' : SortieProductID } ,
                  dataType: 'html',
                  success: function( response ) {
                       $('.foundStock').html(response);
                       $('#currentStockAvailable').val(response);
                  }
            });
            
            
            
            
        return false;
    }); 



   
    $('.entreetable a ,  body a.stoclGeneralEntree').click(function(){        
        var productID = $(this).data('product');
       
        $.ajax( {

          type: "POST",
          url: '/stock/general/loadEntreeHistory/',
          data: {productID : productID  },
          
          success: function( response ) {
              var modal = $('#DetailsModal');
              modal.modal('show');
              modal.find('.modal-body').html(response);      
          }

        });
        
        return false;
    });



  
    $('.sortietable  #loadSortieProductHistory').click(function(){        
        var productID = $(this).data('product');
       
        $.ajax( {

          type: "POST",
          url: '/stock/general/loadHistorySortie/',
          data: {productID : productID  },
          
          success: function( response ) {
              var modal = $('#DetailsModal');
              modal.modal('show');
              modal.find('.modal-body').html(response);      
          }

        });
        
        
        return false;
    });


  
  



  
    
$(document).on('click', '#RemoveRowSortie', function() {
    $(this).closest('.city_quantity').remove();
});




$('body #confirm_stock').click(function(e){
  e.preventDefault();
  e.stopPropagation();
  
         if (  $('body').hasClass('admin-logged') ){
                 doValiateForAdmin();
                 return false;
        }


  
  var error = false;
  $('#confirm_stock_form .sortieValidation').each(function(){
      if( ! $(this).val() ){
        error = true;
        $(this).addClass('has-error'); 
      }else {
        $(this).removeClass('has-error'); 
      }
  });
  
  if(error){
    return false;
  }else{
    $(this).closest('form').submit();
  }
  
  return false;
  
});


      function getProductRest(id){
            $.ajax( {
                  type: "POST",
                  url: '/stock/general/get/rest',
                  data : { 'id' : id } ,
                  dataType: 'html',
                  success: function( response ) {
                       $('.show_meNumber').show();
                       $(".ExistNumber").attr('data-num', response);
                       $('.ExistNumber').html(response);
                  }
            });
        }

        $('#addSortieGeneralStock').on('shown.bs.modal', function (e) {
            var id = $('#addSortieGeneralStock #getSortieNumber').val();
            getProductRest(id);
            return false;
        });

        $('#getSortieNumber').change(function(){
            var id = $(this).val();
            getProductRest(id);
            return false;
        });




function formsortiecheckSortieEmptyFields(){
  var hh = true ;
  $("#formSortieEmbalage #leeetch [type='number']").each(function() {
    if( ! $(this).val() ){
      hh = false;
      $(this).addClass('has-error');
    }else {
      $(this).removeClass('has-error');
    }
  });
  return hh;
}     






   
function checkSortieEmptyFields(){
  var hh = true ;
  $("#leeetch [type='number']").each(function() {
    if( ! $(this).val() ){
      hh = false;
      $(this).addClass('has-error');
    }else {
      $(this).removeClass('has-error');
    }
  });
  return hh;
}     


$('#onclickSendSortie').click(function(){

  var filedsEmptyError = '<div class="alert alert-danger"><strong>تنبيه!</strong> جميع الحقول مهمة ، المرجوا ادخال الكمية او حذف الحقل<button type="button" class="close" aria-label="Close"><span aria-hidden="true">&times;</span></button></div>';

  var ErrorNumber =  '<div class="alert alert-warning"><strong>تنبيه!</strong> المجموع أقل من صفر ، المرجوا الإنتباه ووضع الكمية الصحيحة<button type="button" class="close" aria-label="Close"><span aria-hidden="true">&times;</span></button></div>';


    var emptyCheck = formsortiecheckSortieEmptyFields();
  //  alert(emptyCheck);
    
    if( emptyCheck == false ){
      if (!$('body #leeetch .alert').length){
           $("body #leeetch .alert-danger").show();
          $("body #leeetch").prepend(filedsEmptyError);
      }
      return false;
    } 

    if( emptyCheck == true ){
        $("body #leeetch .alert-danger").hide();
    }

  
    var id  =  $('#formSortieEmbalage').find('[name="productID"]').val();
    
    var sum = 0 ;

    $('#formSortieEmbalage [name="quantities[]"]').each(function(){
        if($(this).val()){
            sum += parseInt($(this).val());    
        }
    });


    var number  = parseInt($('.ExistNumber').text());
    var calc  = number - sum;
    
    if( calc < 0 ){
        alert('الكمية غير متوفرة');
          if (!$('body #leeetch .alert-warning').length){
                $("body #leeetch").prepend(ErrorNumber);
        }
    }else {
        $('#formSortieEmbalage').submit();
    }

    return false;

});
    





/************ instant Search *************/
$('.close_search').click(function(){
  $('.seach-results').hide();
  $('.seachbar').hide();
});

// binnd
$("body #instant-search-holder").bind("keyup , change", function(e) {
    checkAndGetChars();
})

function open_search(){
  $('.seachbar').show();
}

function checkAndGetChars(){
       var searchQuery = $('#instant-search-holder').val();
       if(searchQuery) {
         var count = searchQuery.length;
         if(count > 8 ) {
           $('.seach-results').show();
           return searchFunction(searchQuery);
         }
         $('.seach-results').hide();
         return false;
       }
}





function searchFunction(searchQuery){

      var formdata   = new FormData();
      formdata.append('q',searchQuery);
     
      jQuery.ajax({
                url: '/instant/search',
                type : 'POST',
                contentType : false,
                processData : false,
                dataType : 'html',
                data : formdata,
                beforeSend: function() {
                },
                success : function( response ) {
                    $('.seach-results').html(response)  ;
                },
                error: function (response) {
                  
                }
         });
    return false;
}







/************************** Check validate Stock **********************/
function doValiateForAdmin(){
    
        if( checkEmptyInputsStockSortieConfirm() == true ) {
          return false;
        }
      
        var stockavailable = true;
    
        var currentStockAvailable = $('#currentStockAvailable').val();
        
         if(!currentStockAvailable){
            alert('الكمية غير متوفرة');
            return false;
        }

        if( getTotalofSortie() > currentStockAvailable ) {
            stockavailable = false;
        }
        
        if(!stockavailable){
            alert('الكمية غير متوفرة');
            return false;
        }
      
        $('#confirm_stock_form').submit(); 
}
     


function checkEmptyInputsStockSortieConfirm(){
      var empty = false;
    	$('#confirm_stock_form input').each(function(){
    	   if( ! $(this).hasClass('dontvalidate')){
    	        if( ! $(this).val() ) {
                     $(this).addClass('has-error');
                     empty = true;
                }
    	   }
          
      });
      return empty;
}



function getTotalofSortie(){
    var sum = 0 ;
    $('#confirm_stock_form .sortieValidation').each(function(){
        if($(this).val()){
           sum += parseInt($(this).val());    
        }
    });
    return sum;
}



$('#submitaddembalage').click(function(e){
    
    e.preventDefault();
    e.stopPropagation();
    
  var quantity  =  $('#addembalage').find('[name="quantity"]');
  if(!quantity.val()){
    quantity.addClass('has-error');
    return false;
  }else {
    quantity.removeClass('has-error');
  }
  
  var curr_date =  $('#addembalage').find('.datepickerfromtoday');
  if(curr_date.val()){
      $(this).prop('disabled', true);
       $('#addembalage').submit();
  
  }else{
       curr_date.addClass('has-error');
    return false;
  }
    return false;
  

  
});





















































/*


the idea is to save the data-type in the ul list 
// get the type and use one class for them all
// then use jquery logic

var object = {

  url: function () {
    this.delete();
    console.log('add');
  },


  delete: function () {

    console.log('delete');
  }
};


object.add();
object.add();

*/
