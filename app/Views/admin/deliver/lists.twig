{% extends "admin/layout.twig" %}
{% block title %} {{l.users.1}} {% endblock %}

{% block content %}



<!-- Content area -->
<div class="content">
		
{% include "admin/elements/flash.twig" %}



<div class="navbar navbar-default navbar-xs navbar-component">
                <ul class="nav navbar-nav no-border ">

            <li class="nav-item  {% if type == 'waiting' %} active  {% endif %}">
        <a class="nav-link" href="{{path_for('deliver.orders')}}">
          <span>
            الطلبات الجديدة
          </span> 
        </a>
      </li> 
      <li class="nav-item  {% if type == 'canceled' %} active  {% endif %}">
        <a class="nav-link" href="{{path_for('deliver.orders.canceled')}}">
          <span>الملغية</span>
        </a>
      </li>                  
      <li class="nav-item {% if type == 'delivred'  %} active  {% endif %}">
        <a class="nav-link" href="{{path_for('deliver.orders.delivered')}}">
          <span>تم توزيعها</span>
        </a>
      </li>
      <li class="nav-item {% if type == 'recall'  %} active  {% endif %}">
        <a class="nav-link" href="{{path_for('deliver.orders.recall')}}">
          <span>اعادة الإتصال</span>
        </a>
      </li>  
      <li class="nav-item  {% if type == 'NoAnswer' %} active  {% endif %}">
        <a class="nav-link" href="{{path_for('deliver.orders.NoResponse')}}">
          <span>لا تجيب</span>
        </a>
      </li>              
    

          </ul>

          <div class="navbar-right">
           <ul  class="nav navbar-nav">
            <li><a href="{{path_for('deliver.cash')}}">   الحساب اليومي    </a></li>
            <li><a href="{{path_for('lists.deliver.export')}}">     استخراج كل طلبات الموزع  EXCEL     </a></li>
            <li><a href="javascript:;" id='exportSelectedOnly'>   استخراج  selectione      </a></li>
             <li><select  class=' form-control' id="limitPerPage">
               <option value="10">10</option>
               <option value="20">20</option>
               <option value="30">30</option>
               <option value="40">40</option>
               <option value="50">50</option>
               <option value="100">100</option>
               <option value="100">200</option>
             </select></li>
             <li></li>
           </ul>
          </div>
        </div>




{% if lists is not empty %}



<div class="panel panel-flat responsive-panel">
   
    <table class="table bawaba-tables" >
        <thead>
            <tr>
                <th><b><span class="num_num"></span><input type="checkbox" id="checkAll"></b></th>
                <th><b>رقم الطلب</b></th>
                <th><b>تاريخ</b></th>
                <th><b>الزبون</b></th>
                <th><b>العنوان</b></th>
                <th><b>المنتوجات</b></th>
                {% if type != 'waiting' %}
                <th><b>الحالة</b></th>
                {% endif %}
                <th><b>رؤية المعلومات</b></th>
            </tr>
        </thead>
        <tbody>  
         {% for item in lists %}
         <tr class='row_{{item.id}}'>
             <td><input class="check" type="checkbox" data-item="{{item.id}}"></td>
             <td>#{{item.id}}</td> 
             <td>{{item.created_at}}</td> 
             <td>{{item.name}}
                  <br/>
                  <label class="label bg-danger label_phone"><a href="tel:{{item.tel}}">{{item.tel}}</a></label>
             </td>
             <td>

              {{item.adress}}
              <br/>
              <label class="label bg-purple label_city"> 
              {{item.realcity.city_name}} 
              </label>
             </td>
             <td>
                <table class="list_products">
                        {% set total = 0 %}
                        {% for product in item.products %}
                        <tr>
                            <td> {{product.quanity}} </td>
                            <td> x {{product.product.name}} </td>
                        </tr>
                        {% set total = (total + product.price) %}
                        {% endfor %}
                        <tr> <td colspan="2">المجموع : <b>{{total}}</b> درهم</td></tr>
                </table>
             </td>

             {% if type != 'waiting' %}
             <td><span class="label bg-orange-300 label-statue">   {{item.type}} </span></td>
             {% endif %}
             <td>
              {% if type == 'delivred'%}
              <a type="button" data-action='true' class="btn btn-primary btn-lg loaddata" data-listID='{{item.id}}'>رؤية المعلومات</a>
              {% else %}
              <a type="button" data-action='true' class="btn btn-primary btn-lg loadDeliveractions" data-listID='{{item.id}}'>رؤية المعلومات</a>
              {% endif %}
              
            </td>
             
         </tr>
         {% endfor %}   
        </tbody>
    </table>
</div>
{% endif %}


    </div>


<center>{{pagination|raw}}</center>


{% endblock %}	
            
