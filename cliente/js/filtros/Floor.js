app.filter('Floor', function(){
  return function(value, factor){
    var output = (Math.floor(value / factor) * factor);
    return output;
  };
});