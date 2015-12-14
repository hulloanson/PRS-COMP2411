'use strict';

angular.module('myApp.login', ['ngRoute', 'ngCookies'])

    .config(['$routeProvider', function ($routeProvider) {
        $routeProvider.when('/login', {
            templateUrl: 'login/login.html',
            controller: 'loginCtrl',
            resolve: {
                mess: function ($location, $cookies) {
                    if (!(typeof $cookies.get('token') === 'undefined')) {
                        $location.path('/home');
                    }
                }
            }
        });
        //$routeProvider.when('/login', {
        //    templateUrl: 'login/login.html',
        //    controller: 'loginCtrl'
        //});
    }])

    .controller('loginCtrl', function ($scope, $cookies) {
        //$cookies.put('token', 'ah');
        $cookies.remove('token');
        $scope.tokenIsEmpty = (typeof $cookies.get('token') === 'undefined');
    });