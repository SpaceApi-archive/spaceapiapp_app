<?php

//********************************************************************
// do not edit this section

if(!defined("APPSDIR"))
    die("Direct access is not allowed!");

$app_dir = realpath(dirname(__FILE__));
// remove the full path of the document root
$app_dir = str_replace(ROOTDIR, "", $app_dir);

$page->setActivePage(basename($app_dir));

//********************************************************************

$page->addStylesheet("$app_dir/css/style.css");

$projects = file_get_contents("$app_dir/model.json");
$projects = json_decode($projects);
        
foreach($projects as $project)
{
    $selector_class = preg_replace("/[^a-zA-Z0-9]/i", "_", $project->Title);
    $selector_class = strtolower($selector_class);
    
    $page->addInlineStyle('.'. $selector_class .':after { content: "'. $project->Title .'" }');
    $html = <<<HTML
        <div class="row"><div class="span12">
        <div class="spaceapi-box $selector_class">
            <div class="row">
                 
                <div class="span6" style="padding: 40px 10px 10px 10px; width: 438px;">
                    <p>$project->Description</p>
                    <p>
                        This app was made by %AUTHOR% %TWITTER% %ORGANIZATION%%HOST%.
                        <!--
                            there should be no whitespace between the organization
                            and the host placeholders.
                        -->
                    </p>
                    <p>
                        %SOURCE%
                    </p>
                </div>
                <div class="span6" style="">
                    <img src="$app_dir/img/$project->Screenshot">
                </div>
               
            </div>
        </div>
        </div></div>
HTML;
    
    if(!empty($project->Twitter))
    {   
        $link = '<a href="http://twitter.com/'.$project->Twitter.'" target="_blank">' . $project->Twitter .'</a>';
        $html = str_replace("%TWITTER%", "($link)", $html);
    }
    else
    {
        $html = str_replace("%TWITTER%", "", $html);    
    }

    switch(true)
    {
        case !empty($project->Authorsite):
            
            $author = '<a href="'.$project->Authorsite.'" target="_blank">' . $project->Author .'</a>';  
            break;
     
        // if the author has no private website/blog then his twitter account will be linked   
        case !empty($project->Twitter):
            
            $author = '<a href="http://twitter.com/'.$project->Twitter.'" target="_blank">' . $project->Author .'</a>';
            break;
        
        // if there's no private website nor a twitter account, then ...
        default:
            
            $author = $project->Author;
            
    }
    
    $html = str_replace("%AUTHOR%", $author, $html);
    
    if(!empty($project->Organization))
    {    
        $orga = 'from <a href="'. $project->Organizationsite .'" target="_blank">'. $project->Organization .'</a>';
        $html = str_replace("%ORGANIZATION%", $orga, $html);
    }
    else
    {
        $html = str_replace("%ORGANIZATION%", "", $html);
    }

    if(!empty($project->Website))
    {
        $website = str_replace(array("http://", "https://"), array("",""), $project->Website);
        $website = preg_replace('/\/.*/', '', $website);
        $host = '<a href="'. $project->Website .'" target="_blank">'. $website .'</a>';
        $html = str_replace("%HOST%", " and is hosted on $host ", $html);
    }
    else
    {
        $html = str_replace("%HOST%", "", $html);
    }
    
    $source = "";
    if(!empty($project->Github))
        $source = '<a href="'.$project->Github.'" target="_blank">Github</a> ';
    
    if(!empty($project->Gitorious))
        $source = '<a href="'.$project->Gitorious.'" target="_blank">Gitorious</a> ';

    $html = str_replace("%SOURCE%", " The source code is available on $source.", $html);
      
    $page->addContent($html);
}