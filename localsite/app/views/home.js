define(['jquery', 'backbone', 'templates'],
function($,        Backbone,   templates) {
  var HomeView = Backbone.View.extend({
    el : '.site-content',
    render : function() {
      alert("Rendering Home View");
      this.$el.html(templates['home/main']());
    }
  });
  return HomeView;
});