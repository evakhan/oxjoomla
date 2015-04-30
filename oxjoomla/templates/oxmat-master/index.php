<?php
defined('_JEXEC') or die;
?>
<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="utf-8">
        <jdoc:include type="head" />
        <!-- Mobile support -->
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <link href="//maxcdn.bootstrapcdn.com/bootstrap/3.3.2/css/bootstrap.min.css" rel="stylesheet" type="text/css">

        <!-- Material Design for Bootstrap -->
        
        <link href="<?php echo $this->baseurl ?>/templates/<?php echo $this->template ?>/dist/css/material-wfont.min.css" rel="stylesheet" type="text/css">
        <link href="<?php echo $this->baseurl ?>/templates/<?php echo $this->template ?>/dist/css/ripples.min.css" rel="stylesheet" type="text/css">

        <!-- Dropdown.js -->
        <link href="//cdn.rawgit.com/FezVrasta/dropdown.js/master/jquery.dropdown.css" rel="stylesheet">
        <link rel="stylesheet" href="//maxcdn.bootstrapcdn.com/font-awesome/4.3.0/css/font-awesome.min.css">
        <link href="<?php echo $this->baseurl ?>/templates/<?php echo $this->template ?>/css/style.css" rel="stylesheet" type="text/css">
       
    </head>

    <body>
        <div class="navbar navbar-white navbar-fixed-top" role="navigation">
            <div class="navbar-header">
                <button type="button" class="navbar-toggle" data-toggle="collapse" data-target=".navbar-responsive-collapse">
                    <span class="icon-bar"></span>
                    <span class="icon-bar"></span>
                    <span class="icon-bar"></span>
                </button>
                <a class="navbar-brand" href="javascript:void(0)"><img class="img-responsive" src="<?php echo $this->baseurl ?>/templates/<?php echo $this->template ?>/images/logoipad.png"></a>
            </div>
            <div class="navbar-collapse collapse navbar-responsive-collapse">
                <?php if ($this->countModules( 'search' )) : ?>
                 <div class="col-md-2 src-right">
                 <jdoc:include type="modules" name="search" style="html5" />
          </div>
                <?php endif; ?>
                <jdoc:include type="modules" name="main-menu" style="html5" />
            
          </div>
         
          
        </div>
        <div class="row">
            <jdoc:include type="modules" name="banner" style="html5" />
        </div>
        <?php if ($this->countModules( 'highlights' )) : ?>
        <div class="container wall brack alert-info z-depth-1">

            <jdoc:include type="modules" name="highlights" style="html5" />
        </div>
        <?php endif; ?>
        
        <div class="section multi">
        <div class="container wall z-depth-1">

            <jdoc:include type="modules" name="com-area" style="html5" />
             <jdoc:include type="message" />
             <jdoc:include type="component" />
        </div>
        </div>

        <div class="section">
            <div class="container compare">

                <div class="col-md-6 compare">
                <jdoc:include type="modules" name="compare-1" style="html5" />  
                </div>
                <div class="col-md-6 compare2">
                <jdoc:include type="modules" name="compare-2" style="html5" /> 
            </div>
        </div>
        </div>
        <div class="section">
            <div class="container compare">
                <jdoc:include type="modules" name="contact" style="html5" /> 
            </div>
        </div>

        <div class="section">
            <div class="row botrow">
                <div class="container">
                    <div class="col-md-6">
                        <jdoc:include type="modules" name="footer-1" style="html5" /> 
                    </div>
                    <div class="col-md-6">
                        <jdoc:include type="modules" name="footer-2" style="html5" /> 
                    </div>
                </div>

            </div>
        </div>
        <div class="row footer">
            <div class="container">
                <jdoc:include type="modules" name="copyright" style="html5" /> 
            </div>
        </div>
        <b class="caret"></b>
      <script src="//code.jquery.com/jquery-1.10.2.min.js"></script>
      	<script>
            $(document).ready(function() {
                $.material.init();
                $( ".parent" ).removeClass( "parent" ).addClass( "dropdown" );
                $( ".nav-child" ).addClass( "dropdown-menu" );
                $( ".item-137" ).addClass( "mobilehide" );
                $( ".caret" ).appendTo( ".dropdown-toggle" );
            });
        </script>
        <!-- Twitter Bootstrap -->
        <script src="//cdnjs.cloudflare.com/ajax/libs/twitter-bootstrap/3.3.2/js/bootstrap.min.js"></script>

        <!-- Material Design for Bootstrap -->
      
        <script src="<?php echo $this->baseurl ?>/templates/<?php echo $this->template ?>/dist/js/material.min.js"></script>
        <script src="<?php echo $this->baseurl ?>/templates/<?php echo $this->template ?>/dist/js/ripples.min.js"></script>
        <script src="<?php echo $this->baseurl ?>/templates/<?php echo $this->template ?>/js/script.js"></script>
       
		
       

    </body>
</html>
