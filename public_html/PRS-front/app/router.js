myApp.config(['$routeProvider', function ($routeProvider) {
    var views = ['search', 'home', 'jobs'];
    for (var view in views) {
        $routeProvider.when('/' + view, {
            templateUrl: view + '/' + view + '.html',
            controller: view + 'Ctrl',
            resolve: {
                mess: function ($location, $cookies) {
                    if (!(typeof $cookies.get('token') === 'undefined')) {
                        $location.path('/home');
                    } else {
                        $location.path('/login');
                    }
                }
            }
        });
    }
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
    $routeProvider.otherwise({redirectTo: '/login'});
}]);