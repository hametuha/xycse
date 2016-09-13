/* global Xycse:false */
angular.module('xycse', ['ui.sortable'])
  .controller('xycseForm', ['$scope', '$http', function ($scope, $http) {

    $scope.option = Xycse.option;

    $scope.post_types = Xycse.post_types;

    $scope.taxonomies = Xycse.taxonomies;

    $scope.dates = Xycse.dates;

    $scope.error = false;

    $scope.message = [];

    $scope.loading = false;

    $scope.current = {
      name    : '',
      subject : 'post',
      object  : '',
      taxonomy: '',
      date    : 'no'
    };

    $scope.add = function () {
      $scope.error = false;
      $scope.message = [];
      // Validation.
      var duplicated = false;
      angular.forEach($scope.option, function (option) {
        if (option.name == $scope.current.name) {
          duplicated = true;
        }
      });
      if (duplicated) {
        $scope.message.push(Xycse.message.unique);
      }
      if (/\A[a-zA-Z0-9-_]+\z/.test($scope.current.name)) {
        $scope.message.push(Xycse.message.alnum)
      }
      if (!$scope.current.name.length || !$scope.current.subject.length) {
        $scope.message.push(Xycse.message.require);
      }
      if (!$scope.current.object.length && !$scope.current.taxonomy.length) {
        $scope.message.push(Xycse.message.unsatisfied);
      }
      if ($scope.message.length) {
        $scope.error = true;
      } else {
        $scope.option.push(angular.copy($scope.current));
      }
    };

    $scope.remove = function (i) {
      $scope.option.splice(i, 1);
    };

    $scope.save = function(){
      $scope.error = false;
      $scope.message = [];
      $scope.loading = true;
      $http.post(Xycse.endpoint, $scope.option)
        .success(function(data, status, headers, config) {
          $scope.message.push(data.message);
          $scope.loading = false;
        })
        .error(function(data, status, headers, config) {
          console.log(data);
          $scope.error = true;
          $scope.message.push( 'Error' );
          $scope.loading = false;
        });

    }
  }])
  .directive('xycseSpan', function () {
    return {
      restrict: 'E',
      scope   : {
        item: '='
      },
      template: '<span class="xycse-span">{{description}}</spanc>',
      link    : function ($scope, $element, attr) {


        $scope.description = '';

        var subject = '',
            object = '',
            taxonomyLabel = '',
            date = '';
        angular.forEach(Xycse.post_types, function (post_type) {
          if ($scope.item.subject == post_type.name) {
            subject = post_type.label + ' belongs';
          }
          if($scope.item.object == post_type.name){
            object = ' to ' + post_type.label;
          }
        });

        switch ($scope.item.date) {
          case 'at':
            date = ' at TIME';
            break;
          case 'range':
            date = ' from START till END';
            break;
          default:
            // Do nothing.
            break;
        }
        taxonomyLabel = '';
        if ($scope.item.taxonomy) {
          angular.forEach(Xycse.taxonomies, function (taxonomy) {
            if ($scope.item.taxonomy == taxonomy.name) {
              taxonomyLabel = ' as ' + taxonomy.label;
            }
          });
        }
        $scope.description = subject + object + taxonomyLabel + date;
      }
    };
  });