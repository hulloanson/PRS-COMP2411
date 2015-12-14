'use strict';

angular.module('myApp.search', ['ngRoute'])

    .controller('searchCtrl', function ($scope) {
        $scope.searchResults = [
            {title: "ABC", authors: "BCD, EFG", progress: "Abstract", status: "Reviewed"},
            {title: "Lamer", authors: "Mr. Bean", progress: "Paper", status: "Pending"}
        ]
    });