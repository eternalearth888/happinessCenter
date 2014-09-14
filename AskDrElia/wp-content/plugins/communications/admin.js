
function countit(what){
  document.getElementById('charcount').innerHTML = 120 - document.getElementById('sendtxtextarea').value.length;
  }

var commsinitresults=function(results)
  {
  eval(results);
  document.getElementById("managegroups").style.display  ='none';
  if(document.getElementById("addcontact") != null)
    {
    document.getElementById("addcontact").style.display  ='none';
    }
  document.getElementById("editcontact").style.display  ='block';
  }

var commsgroupresults=function(results)
  {
  eval(results);
  document.getElementById("addgroup").style.display  ='none';
  document.getElementById("editgroup").style.display  ='block';
  }

var managegroupresults=function(results)
  {
  eval(results);
  if(document.getElementById("addcontact") != null)
    {
    document.getElementById("addcontact").style.display  ='none';
    }
  document.getElementById("editcontact").style.display  ='none';
  document.getElementById("managegroups").style.display  ='block';
  }

function editcontact(id)
  {
  ajax.post("index.php",commsinitresults,("ajax=true&comms=true&id="+id));
  }

function editgroup(id)
  {
  ajax.post("index.php",commsgroupresults,("ajax=true&comms=true&groupid="+id));
  }

function manage_user_groups(id,image_id)
  {
  state = document.getElementById(id).style.display; 
  if(state != 'block')
    {
    document.getElementById(id).style.display = 'block';
    document.getElementById(image_id).src = '../wp-content/plugins/communications/images/icon_window_collapse.gif';
    }
    else
      {
      document.getElementById(id).style.display = 'none';
      document.getElementById(image_id).src = '../wp-content/plugins/communications/images/icon_window_expand.gif';
      }
  return false;
  }
  
function addproject()
  {
  if(document.getElementById("addcontact") != null)
    {
    document.getElementById("addcontact").style.display  ='block';
    }
  }

function pageload()
  {
  if(document.getElementById("t5").value == 0)
    {
    }
  }
  
function addcontact()
  {
  document.getElementById("editcontact").style.display  ='none';
  if(document.getElementById("addcontact") != null)
    {
    document.getElementById("addcontact").style.display  ='block';
    }
  }
  
var getprojectdata=function(results)
  {
  eval(results);
  if(document.getElementById("addcontact") != null)
    {
    document.getElementById("addproject").style.display  ='none';
    }
  document.getElementById("editproject").style.display  ='block';
  }
  
function editproject(id)
  {
  ajax.post("index.php",getprojectdata,("ajax=true&project=true&id="+id));
  }

function group_checkboxes(state)
  {
  switch(state)
    {
    case "show":
    document.getElementById("group_checkboxes").style.display  ='block';
    break;
    
    case "hide":
    document.getElementById("group_checkboxes").style.display  ='none';
    break;
    }
  }