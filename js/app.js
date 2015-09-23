var App = angular.module('App',['720kb.datepicker']).filter('tel', function (){}); 

    App.controller('CustomerController', function($scope, $http) {
        $scope.customersList = [];
        // get customers list
        getCustomersList();
        
        $scope.tempCustomer = {};
        $scope.editedCustomer = {};
        $scope.tempOrder = {};
        $scope.newCustomer = {name: '', phone: '', address: ''};
        $scope.disableEditButtons = false;
        
        // add new customer
        $scope.addCustomer = function (customer) {
            //console.log(customer); return;
            $http.post('index.php/customer/addcustomer', customer).success(function(data) {
                $scope.customersList.push(data);
                $scope.newCustomer = {name: '', phone: '', address: ''};
            });
        };
        
        // turn on edit mode for customer
        $scope.editCustomer = function(customer) {
            $scope.editedCustomer = angular.copy(customer);
            $scope.tempCustomer = angular.copy(customer);
            $scope.tempCustomer.orders = []; 
            $scope.disableEditButtons = true;
        };
        
        // save changes in edited customer + save added orders
        $scope.saveCustomer = function(customer) {
            var index = _.findIndex($scope.customersList, $scope.editedCustomer); 
            
            console.log('$scope.tempCustomer', $scope.tempCustomer.orders);
            $http.put('index.php/customer/editcustomer/' + $scope.tempCustomer.id, $scope.tempCustomer).success(function(data, status, headers, config){
                getCustomersList();
                //$scope.customersList[index] = angular.copy($scope.tempCustomer);
            });
            $scope.tempCustomer = {};
            $scope.editedCustomer = {};
            $scope.disableEditButtons = false;
        };
        
        // add new order to customer orders list
        $scope.addOrder = function(order) {
            console.log('$scope.tempOrder', $scope.tempOrder);
            var arrLength = $scope.tempCustomer.orders.length;
            $scope.tempCustomer.orders[arrLength] = {};
            $scope.tempCustomer.orders[arrLength].postedAt = $scope.tempOrder.posted_at;
            $scope.tempCustomer.orders[arrLength].amount = $scope.tempOrder.amount;
            $scope.tempCustomer.orders[arrLength].paidAt = $scope.tempOrder.paid_at;
            
            console.log('$scope.tempCustomer.orders', $scope.tempCustomer.orders);
            $scope.tempOrder = {};
        };
        
        // cancel saving of edited customer
        $scope.cancelSaving = function(customer) {
            var index = _.findIndex($scope.customersList, $scope.editedCustomer);
            $scope.customersList[index] = angular.copy($scope.editedCustomer);
            $scope.tempCustomer = {};
            $scope.editedCustomer = {};
            $scope.tempOrder = {};
            $scope.disableEditButtons = false;
        };
        
        // delete customer from list
        $scope.deleteCustomer = function(customer) {
            var answer = confirm('Are you sure you want to delete this customer?');
            if (answer) {
                var index = $scope.customersList.indexOf(customer);
                if (customer.id) {
                    $http.delete('index.php/customer/deletecustomer/'+customer.id).success(function(data){
                        $scope.customersList.splice(index, 1);
                    });
                } else {
                    $scope.customersList.splice(index, 1);
                }                
            }            
        };
        
        function getCustomersList() {
            $http.get('index.php/customer/getcustomerslist').success(function(data, status, headers, configdata){
                $scope.customersList = data;
            });
        }
    });