function addIndex() {
  var nonExistantTags = 0;
  var indices = [];
  for(var i = 1; i <= 6; i++) {
      if($('h'+i).not('.nonumbering').size() > 0) {
          break;
      }
      nonExistantTags++;
  }
  // jQuery will give all the HNs in document order
  $(':header').not('.nonumbering').each(function(i,e) {
      var hIndex = parseInt(this.nodeName.substring(1)) - 1 - nonExistantTags;

      // just found a levelUp event
      if (indices.length - 1 > hIndex) {
        indices= indices.slice(0, hIndex + 1 );
      }

      // just found a levelDown event
      if (indices[hIndex] == undefined) {
            indices[hIndex] = 0;
      }
      if(typeof $(this).attr('data-sectionid') != "undefined") {
            indices[hIndex] = parseInt($(this).attr('data-sectionid')) - 1;
      }

      // count + 1 at current level
      indices[hIndex]++;

      // display the full position in the hierarchy
      $(this).prepend(indices.join(".")+". ");

  });
}

$(document).ready(function() {
  addIndex();
});