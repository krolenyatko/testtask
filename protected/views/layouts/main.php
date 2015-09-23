<?php /* @var $this Controller */ ?>
<!DOCTYPE html>
<html ng-app="App">
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8">
    <meta name="language" content="en">        
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <base href="<?php echo Yii::app()->createAbsoluteUrl('/'); ?>/">
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.5/css/bootstrap.min.css">
    <link rel="stylesheet" href="<?php echo Yii::app()->request->baseUrl; ?>/css/angular-datepicker.css">
    <title><?php echo CHtml::encode($this->pageTitle); ?></title>
</head>

<body>
<div class="container">
    <div class="row">
        <div ng-controller="CustomerController" class="col-md-12">
            <h3 class="text-center">Customers</h3>
            <div ng-if="customersList.length <= 0">There are no any customers yet.</div>
            <div ng-if="customersList.length > 0">
                <div ng-repeat="customer in customersList" class="panel panel-default">
                    <div class="panel-heading">
                        Customer | ID: {{customer.id}}
                        <span ng-hide="disableEditButtons" class="pull-right">
                            <button class="btn btn-xs btn-primary" ng-click="editMode=true; editCustomer(customer);">
                                <i class="glyphicon glyphicon-pencil"></i>
                            </button>
                            <button class="btn btn-xs btn-danger" ng-click="deleteCustomer(customer)">
                                <i class="glyphicon glyphicon-remove"></i>
                            </button>
                        </span>
                    </div>
                    <div class="panel-body">
                        <div class="col-md-4">
                            <p><strong>Customer info</strong></p>
                            <p>
                                <i class="glyphicon glyphicon-user"></i>&nbsp;
                                <span ng-show="!editMode">{{customer.name}}</span>
                                <span ng-show="editMode"><input type="text" class="form-control" required ng-model="tempCustomer.name" /></span>
                            </p>
                            <p>
                                <i class="glyphicon glyphicon-phone"></i>&nbsp;
                                <span ng-show="!editMode">{{customer.phone}}</span>
                                <span ng-show="editMode"><input type="text" class="form-control" required ng-model="tempCustomer.phone" /></span>
                            </p>
                            <p>
                                <i class="glyphicon glyphicon-envelope"></i>&nbsp;
                                <span ng-show="!editMode">{{customer.address}}</span>
                                <span ng-show="editMode"><textarea class="form-control" required ng-model="tempCustomer.address"></textarea></span>
                            </p>
                        </div>
                        <div class="col-md-8">
                            <table ng-if="(customer.orders && customer.orders.length > 0) || editMode" ng-model="customer.orders" class="table">
                                <tr colspan="4"><p class="text-center">Orders</p></tr>
                                <tr>
                                    <th>&nbsp;</th>
                                    <th>Posted at</th>
                                    <th>Amount</th>
                                    <th>Paid at</th>
                                </tr>
                                <tr ng-repeat="order in customer.orders">
                                    <td>#{{$index+1}}</td>
                                    <td>{{order.posted_at}}</td>
                                    <td>{{order.amount}}</td>
                                    <td>{{order.paid_at}}</td>
                                </tr>
                                <tr ng-if="editMode && tempCustomer.orders.length" ng-repeat="order in tempCustomer.orders">
                                    <td>#{{customer.orders.length + $index + 1}}</td>
                                    <td>{{order.postedAt}}</td>
                                    <td>{{order.amount}}</td>
                                    <td>{{order.paidAt}}</td>
                                </tr>
                                <tr ng-if="editMode">
                                    <td>&nbsp;</td>
                                    <td>
                                        <datepicker date-format="yyyy-MM-dd">
                                            <input readonly="true" ng-model="tempOrder.posted_at" class="form-control" type="text"/>
                                        </datepicker>
                                    </td>
                                    <td>
                                        <input type="number" ng-model="tempOrder.amount" class="form-control" required />
                                    </td>
                                    <td>
                                        <datepicker date-min-limit="{{tempOrder.posted_at}}" date-format="yyyy-MM-dd">
                                            <input readonly="true" ng-model="tempOrder.paid_at" class="form-control" type="text"/>
                                        </datepicker>
                                    </td>
                                </tr>
                            </table>
                            <button ng-if="editMode" class="btn btn-sm btn-default pull-right" ng-click="addOrder(tempOrder)">
                                Add order&nbsp;<i class="glyphicon glyphicon-plus"></i>
                            </button>
                        </div>
                    </div>
                    <div ng-if="editMode" class="panel-footer">
                        <span class="pull-center">
                        <button  class="btn btn-primary" ng-click="editMode=false; saveCustomer(customer)">
                            Save&nbsp;<i class="glyphicon glyphicon-floppy-disk"></i>
                        </button>
                        <button  class="btn btn-danger" ng-click="editMode=false; cancelSaving(customer)">
                            Cancel&nbsp;<i class="glyphicon glyphicon-remove"></i>
                        </button>
                        </span>
                    </div>
                </div>                    
            </div>
                    
            <form novalidate class="form-horizontal" name="addCustomerForm">
                <div class="form-group" ng-class="{'has-error': (addCustomerForm.name.$touched || addCustomerForm.$submitted) && addCustomerForm.name.$invalid}">        
                    <label for="name" class="col-sm-2 control-label">Name:</label>
                    <div class="col-sm-10">
                        <input ng-model="newCustomer.name" type="text" class="form-control" id="name" name='name' required placeholder="customer name">
                        <div class="error-messages" ng-show="addCustomerForm.name.$touched || addCustomerForm.$submitted" role="alert">
                            <p class="help-block" ng-show="addCustomerForm.name.$error.required">Value is required and can't be empty</p>
                        </div>
                    </div>
                </div>
                <div class="form-group" ng-class="{'has-error': (addCustomerForm.phone.$touched || addCustomerForm.$submitted) && addCustomerForm.phone.$invalid}">
                    <label for="phone" class="col-sm-2 control-label">Phone:</label>
                    <div class="col-sm-10">
                        <input ng-model="newCustomer.phone" type="text" class="form-control" id="phone" name='phone' required placeholder="555-55-55">
                        <div class="error-messages" ng-if="addCustomerForm.phone.$touched || addCustomerForm.$submitted" role="alert">
                            <p class="help-block" ng-show="addCustomerForm.phone.$error.required">Value is required and can't be empty</p>
                        </div>
                    </div>
                </div>
                <div class="form-group">
                    <label for="address" class="col-sm-2 control-label">Address:</label>
                     <div class="col-sm-10">
                        <textarea ng-model="newCustomer.address" class="form-control" id="address" name='address' placeholder="customer's address"></textarea>
                    </div>
                </div>
                <div class="form-group">
                    <div class="col-sm-offset-2 col-sm-10">
                        <button ng-disabled="newCustomer.name == '' && newCustomer.phone == ''" type="submit" class="btn btn-default" ng-click="e.preventDefault();addCustomer(newCustomer)">Add customer</button>
                    </div>
                </div>
            </form>
        </div>
    </div>    
</div>
    
<script src="https://ajax.googleapis.com/ajax/libs/angularjs/1.2.7/angular.min.js"></script>
<script src="//ajax.googleapis.com/ajax/libs/angularjs/1.2.7/angular-route.js"></script>
<script src="https://ajax.googleapis.com/ajax/libs/angularjs/1.2.7/angular-resource.min.js"></script>
<script src="<?php echo Yii::app()->request->baseUrl; ?>/js/vendor/angular-datepicker.js"></script>
<script src="https://ajax.googleapis.com/ajax/libs/jquery/1.11.3/jquery.min.js"></script>
<script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.5/js/bootstrap.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/lodash.js/3.10.1/lodash.js"></script>
<script src="<?php echo Yii::app()->request->baseUrl; ?>/js/app.js"></script>
<script src="<?php echo Yii::app()->request->baseUrl; ?>/js/CustomerController.js"></script>
</body>
</html>
