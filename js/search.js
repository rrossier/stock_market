$(document).ready(function(){
  $('#q').typeahead({
    source: function (query, process) {
      $.ajax({
        url: '../ajax/search.php',
        type: "POST",
        data:"q="+query,
        dataType: "json",
        success: function(data) {
          var resultList = data.map(function (item) {
            var link = { href: item.href, title: item.title };
            return JSON.stringify(link);
          });
          return process(resultList);
        }
      });
    },
    matcher: function (obj) {
  query=$("#q").val();
        var item = JSON.parse(obj);
        return ~item.title.toLowerCase().indexOf(this.query.toLowerCase())
    },

    sorter: function (items) {          
       var beginswith = [], caseSensitive = [], caseInsensitive = [], item;
       while (link = items.shift()) {
            var item = JSON.parse(link);
            if (!item.title.toLowerCase().indexOf(this.query.toLowerCase())) beginswith.push(JSON.stringify(item));
            else if (~item.title.indexOf(this.query)) caseSensitive.push(JSON.stringify(item));
            else caseInsensitive.push(JSON.stringify(item));
        }

        return beginswith.concat(caseSensitive, caseInsensitive)

    },

    highlighter: function (link) {
        var item = JSON.parse(link);
        var query = this.query.replace(/[\-\[\]{}()*+?.,\\\^$|#\s]/g, '\\$&')
        var title= item.title.replace(new RegExp('(' + query + ')', 'ig'), function ($1, match) {
            return '<strong>' + match + '</strong>'
        })
        return title;
    },

    updater: function (link) {
        var item = JSON.parse(link);
       //$("#q").attr('href', item.href);
       //var str='<a href="'+item.href+'">'+item.title+'</a>';
       window.location.href = item.href;
       return item.title;
    }
  });
});