var currentid;
var posx;
var posy;

function hide_show_edit(state)
  {
  switch(state)
    {
    case 'show':
    document.getElementById('editevent').style.display="block";
    document.getElementById('closeevent').style.display="block";
    break;

    case 'hide':
    document.getElementById('editevent').style.display="none";
    document.getElementById('closeevent').style.display="none";
    break;
    }
  }

function closeevent()
  {
  document.getElementById('eventdata').style.display="none";
  return false;
  }

function displayevent()
  {
  document.getElementById('eventdata').style.display="block";
  return false;
  }

function edit()
  {
  projectid = document.getElementById('project_note_id').value;
  note = document.getElementById('resizing_textarea').value;
  ajax.post("index.php",save_note,"ajax=true&save=true&projectid="+projectid+"&note="+note);
  document.getElementById('note_saving_anim').style.display = "block";
  return false;
  }

var save_note=function(results)
  {
  document.getElementById('note_saving_anim').style.display = "none";
  return true;
  }

var get_event=function(results)
  {
  eval(results);
  if(document.getElementById('eventdata').style.display != "block")
    {
    document.getElementById('eventdata').style.display = "block";
    document.getElementById('eventdata').style.top = (parseInt(posy)-50) + "px";
    document.getElementById('eventdata').style.left = (parseInt(posx)-75) + "px";
    }
  project_notes = project_notes.replace(/<br \/>/g,"\n");
  document.getElementById('resizing_textarea').value = project_notes;
  document.getElementById('project_note_id').value = project_id;
  document.getElementById('project_title').innerHTML = project_title;
  }

function displaynotes(projectid,evt)
  {
  ajax.post("index.php",get_event,"ajax=true&comms=true&projectid="+projectid+"");
  if (!e) var e = (window.event) ? window.event : evt;
  if (e.pageX || e.pageY)
    {
    posx = e.pageX;
    posy = e.pageY;
    }
    else if (e.clientX || e.clientY)
      {
      posx = e.clientX + document.body.scrollLeft;
      posy = e.clientY + document.body.scrollTop;
      }
  return false;
  }
  
jQuery(document).ready(
  function()
  {
  jQuery('div#eventdata').Draggable(
      {
      handle: 'div.topbar',
      opacity: 0.5,
      zIndex: 20
      }
    );
  jQuery('#resizing_textarea').Autoexpand([288,800]);
  //$('textarea').Autoexpand(400);
  }
);