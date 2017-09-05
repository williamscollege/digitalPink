<?php
/**
 *
 * Created by Cheryl Handsaker.
 * User: cph2
 * Date: 8/25/17
 * Purpose: Generate the digital gallery for the PINK Exhibit from a tsv file provided by WCMA
 *
 */

function generateGallerySidecards ()
{
    /***
     * Takes the rank of the image and returns the filename of the appropriate thumbnail
     *
     * @param $rank - the rank of the image
     *
     */
    function chooseThumbnail ($rank) {

        $i = ceil ($rank / 100); // round up to the nearest integer

        switch ($i) {
            case 1:
                return ("1-100.png");
                break;
            case 2:
                return ("101-200.png");;
                break;
            case 3:
                return ("201-300.png");
                break;
            case 4:
                return ("301-400.png");
                break;
            case 5:
                return ("401-500.png");
                break;
            default:
                return ("over500.png");
        }
    }

//Pull exhibit data from tab-separated file into an associative array
    $checklistfilename = "data/pinkexhibit.tsv";

    $header = null;
    $pinkdata = array();
    $lines = file($checklistfilename);

    foreach ($lines as $line) {
        $values = str_getcsv($line, "\t");
        if (!$header) $header = $values;
        else $pinkdata[] = array_combine($header, $values);
    }

//Pull algorithm data from tab-separated file into an associative array
    $algorithmfilename = "data/pink-algorithms.tsv";

    $header = null;
    $pinkalgorithms = array();
    $lines = file($algorithmfilename);

    foreach ($lines as $line) {
        $values = str_getcsv($line, "\t");
        if (!$header) $header = $values;
        else $pinkalgorithms[] = array_combine($header, $values);
    }

// Generate the ready function that contains the gallery scripts for each image
    $pinkgallery = '<script type="text/javascript">';
    $pinkgallery .= '$(document).ready(function (){ ';

// Generate the lightGallery javascript for each image in the exhibit
    foreach ($pinkdata as $pinkimage) {

        // Tie the scripts to the html element for each dynamic lightGallery based on the filename
        $pinkgallery .= "
        $('#_" . preg_replace("/[^A-Za-z0-9 ]/", '', $pinkimage['filename']) . "').on('click', function() { 
    ";
        $pinkgallery .= "$(this).lightGallery({
                dynamic: true,
                dynamicEl: [{";

        //Create the entry for the original image
        $pinkgallery .= '
                        "src":' . " 'images/original/" . $pinkimage['filename'] . ".jpg',";
        $pinkgallery .= "
                        'thumb': 'images/thumbs/" . chooseThumbnail($pinkimage['mean-rank']) . "',";
        $pinkgallery .= "
                 'subHtml': '" . $pinkimage['image-info'] . "'
                 ";
        $pinkgallery .= '            }';

        // Iterate through each algorith and generate the sidecar image for the lightGallery
        foreach ($pinkalgorithms as $algorithm) {
            // Sample of the loop generated code
            //, {
            //"src": 'images/plain/IMG_78.38.31.png',
            //'thumb': 'images/thumbs/PinkCircles2.png',
            //'subHtml': "<h4>Plain</h4><p>Lots of information about the Plain Algortithm</p>"
            // }

            // Original are jpg but sidecars are png
            $sidecarImage = $algorithm['name'] . "/" . $pinkimage['filename'] .".png";

            // Thumbnails are stored in the algorithm directory, prefaced by thumb- and are assumed to be svg images
            $rankValue = $algorithm['name']."-rank";
            $sidecarThumb = "/thumbs/" . chooseThumbnail($pinkimage[$rankValue]);

            $pinkgallery .= ', {
                        "src": ';
            $pinkgallery .= "'images/" . $sidecarImage . "',
                        'thumb': 'images/" . $sidecarThumb. "',";
            $pinkgallery .= "
                         'subHtml': " . "'" . $algorithm['description'] . "'
                         }";
        }

        //Close the script for each image
        $pinkgallery .= "],
                thumbWidth: 50,
                thumbHeight: '50px',
                thumbContHeight: 70,
                loadVimeoThumbnail: false
            });
        });
        ";
    }
    $pinkgallery .= '})
    
    </script>';

    echo $pinkgallery;

}

function generateGalleryDisplay ()
{

//Pull exhibit data from tab-separated file into an associative array
    $checklistfilename = "data/pinkexhibit.tsv";

    $header = null;
    $pinkdata = array();
    $lines = file($checklistfilename);

    foreach ($lines as $line) {
        $values = str_getcsv($line, "\t");
        if (!$header) $header = $values;
        else $pinkdata[] = array_combine($header, $values);
    }

    //Pull algorithm data from tab-separated file into an associative array
    $algorithmfilename = "data/pink-algorithms.tsv";

    $header = null;
    $pinkalgorithms = array();
    $lines = file($algorithmfilename);

    foreach ($lines as $line) {
        $values = str_getcsv($line, "\t");
        if (!$header) $header = $values;
        else $pinkalgorithms[] = array_combine($header, $values);
    }


    // Include the gallery definition
    $pinkgallery = '<section class="gallery">';

    // Generate the code for the gallery, with sorting and layout
    foreach ($pinkdata as $pinkimage) {
        $pinkgallery .= '
            <article class="pinkart">
                <a href="javascript:void(0)" id="_'. preg_replace("/[^A-Za-z0-9 ]/", '', $pinkimage['filename']) .'">
                        <img src="images/original/'. $pinkimage['filename']. '.jpg " alt="">
                </a>
                <span class="meanrank">' . $pinkimage['mean-rank'] . '</span>
                ';

                foreach ($pinkalgorithms as $algorithm) {
                    $rankValue = $algorithm['name']."-rank";
                    $pinkgallery .= '<span class="'. $algorithm['name'] . 'rank">' . $pinkimage[$rankValue] . '</span>
                    ';
                }
        $pinkgallery .= '</article>';
    }

    $pinkgallery .= '</section> <!-- .gallery -->';

    echo $pinkgallery;

}