'use strict';

// Declare app level module which depends on views, and components
var myApp = angular.module('myApp', [
    'ngRoute',
    'ngCookies',
    'myApp.home',
    'myApp.search',
    'myApp.jobs',
    'myApp.login'
    //'myApp.version'
]);



myApp.controller('indexController', function ($scope) {
    $scope.navBarItem = [{name: 'Home', view: 'home'}, {name: 'Search', view: 'search'},
        {name: 'Jobs', view: 'jobs'}];
});
