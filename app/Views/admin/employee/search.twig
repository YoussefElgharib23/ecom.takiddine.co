{% extends "admin/layout.twig" %}
{% block title %} {{l.users.1}} {% endblock %}

{% block content %}

<!-- Content area -->
<div class="content">
    





<div class="navbar navbar-default navbar-xs navbar-component">
                <ul class="nav navbar-nav no-border visible-xs-block">
                  <li><a class="text-center collapsed" data-toggle="collapse" data-target="#navbar-filter"><i class="icon-menu7"></i></a></li>
                </ul>

                <div class="navbar-collapse collapse" id="navbar-filter">
                  <p class="navbar-text"> فلتر  </p>
                  <ul class="nav navbar-nav">
                           <li class="nav-item {% if type == 'waiting'  %} active {% endif %}" >
            <a class="nav-link" href="{{path_for('employee.orders')}}">
              <i class="icon-cup2"></i>
              <span>طلبات قيد المعالجة</span>
            </a>
          </li>
          <li class="nav-item {% if type == 'canceled'  %} active {% endif %}">
            <a class="nav-link" href="{{path_for('employee.orders.canceled')}}">
             <i class="icon-construction"></i>
              <span>الطلبات الملغية</span>
            </a>
          </li> 
          <li class="nav-item {% if type == 'recall'  %} active {% endif %}">
            <a class="nav-link" href="{{path_for('employee.orders.recall')}}">
              <i class="icon-mobile"></i>
              <span>طلبات اعادة الإتصال</span>
            </a>
          </li>                  
        

       <li class="dropdown nav-item {% if type == 'NoAnswer'  %} active {% endif %}">
            <a href="#" class="dropdown-toggle" data-toggle="dropdown" aria-expanded="false">
              <i class="icon-briefcase"></i> 
                طلبات لا تجيب
                <span class="caret"></span>
            </a>
            <ul class="dropdown-menu dropdown-menu-right ">
                <li><a href="{{ path_for('employee.orders.NoResponse') }}">  الطلبات لا تجيب </a></li>
                <li><a href="{{ path_for('employee.orders.NoResponse')}}?v=1">  لا يجيب المرحلة الأولى </a></li>
                <li><a href="{{ path_for('employee.orders.NoResponse')}}?v=2">  لا يجيب المرحلة الثانية </a></li>
                <li><a href="{{ path_for('employee.orders.NoResponse')}}?v=3"> لا يجيب المرحلة الثالثة  </a></li>
                <li><a href="{{ path_for('employee.orders.NoResponse')}}?v=4">  لا يجيب المرحلة الرابعة </a></li>  
                <li><a href="{{ path_for('employee.orders.NoResponse')}}?v=5"> لا يجيب المرحلة الخامسة  </a></li>  
                <li><a href="{{ path_for('employee.orders.NoResponse')}}?v=6"> لا يجيب المرحلة السادسة  </a></li>  
                <li><a href="{{ path_for('employee.orders.NoResponse')}}?v=7"> لا يجيب المرحلة السابعة  </a></li>  
            </ul>
        </li>

          

          </ul>

                  <div class="navbar-right">
                   <ul  class="nav navbar-nav">


                    <li><a href="{{path_for('lists.create')}}?returnURI={{EXIST_URL}}">   + طلب جديد  </a></li>


                    <li><select  class=' form-control' id="limitPerPage">
                       <option value="10">10</option>
                       <option value="20">20</option>
                       <option value="30">30</option>
                       <option value="40">40</option>
                       <option value="50">50</option>
                       <option value="100">100</option>
                       <option value="100">200</option>
                    </select>
                    </li>

            <li><a href="javascript:;" id='exportSelectedOnly'>   استخراج  selectione      </a></li>


                     <li><a href="javascript:;" id="show_search_box"><i class="icon-search4 position-left"></i>بحث </a></li>
                   </ul>
                  </div>
                </div>
              </div>



{% include "admin/elements/flash.twig" %}


<div class="panel panel-flat" id="search_box">
    <div class="panel-heading">
        <h5 class="panel-title">


          ادخل رقم الهاتف<a class="heading-elements-toggle"><i class="icon-more"></i></a></h5>
    </div>

    <div class="panel-body">
        <form action="{{path_for('pages.find')}}" method="GET" class="main-search">
            <div class="input-group content-group">
                <div class="has-feedback has-feedback-left">
                    <input value="{{number}}" type="text" class="form-control input-xlg" placeholder="رقم الهاتف" name="q">
                    <div class="form-control-feedback">
                        <i class="icon-search4 text-muted text-size-base"></i>
                    </div>
                </div>

                <div class="input-group-btn">
                    <button type="submit" class="btn btn-primary btn-xlg">بحث</button>
                </div>
            </div>
        </form>
    </div>
</div>




<div class="mg-t-25">
   <div class="table-responsive">
    <table class="table table-bordered bawaba-tables">
        <thead>
            <tr>
                <th><b><input type="checkbox" id="checkAll" /></b></th>
                <th><b>تاريخ </b></th>
                <th><b>الإسم ورقم الهاتف</b></th>
                <th><b>العنوان</b></th>
                <th><b>الحالة</b></th>
                <th><b>المنتوج</b></th>
                <th><b>المصدر</b></th>
            </tr>
        </thead>
        <tbody>  
        
        
        {% for item in lists %}
        
        {% if item.select == 'new' %}
        <tr class='listingOrdersTR'>
             <td>
              <div class="box">
              <span class="checkmark"></span>
              <input class='check' type="checkbox" data-item='{{item.id}}'>
              </div>
             </td>
             <td>{{item.created_at}}</td>
             <td>{{item.name}}
             <br>
             <a href="tel:{{item.tel}}" class="tel">
             <label for="" class="label label-primary label-city-op">{{item.tel}}</label>
             </a>
             </td>
             <td>{{item.adress}} 
             <br>
             <label for="" class="label label-success label-city-op">{{item.city}}</label>
             </td>
             <td> <label for="" class="city-data">{{item.type}}</label> </td>
             <td>{{item.ProductReference}} X {{item.quantity}} = {{item.price}} </td>    
             <td>{{item.source}} </td>    
         </tr>
         {% else %} 
            <tr class='listingOrdersTR'>
             <td>
              <div class="box">
              <span class="checkmark"></span>
              <input class='check' type="checkbox" data-item='{{item.id}}'>
              </div>
             </td>
             <td>{{item.created_at}}</td>
             <td>{{item.name}}
             <br>
             <a href="tel:{{item.tel}}" class="tel">
             <label  class="label label-primary label-city-op">{{item.tel}}</label>
             </a>
             </td>
             <td>{{item.adress}} 
                    <span class="label label-success city-data">{{item.cityName}}</span>
             <br>
             </td>
             <td> <label for="" class="city-data">{{item.type}}</label> </td>
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
                    <tr> <td colspan="2">المجموع : {{total}} درهم</td></tr>
             </table>
             </td>    
             <td>{{item.source}} </td>    
             </tr>
            
         {% endif %}
         {% endfor %}   
        </tbody>
    </table>
    </div>
</div>




    </div>
</div>


</div>
   



{% endblock %}  
            