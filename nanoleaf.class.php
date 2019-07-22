<?php
// *********************************************************************************************************************************
//
// nanoleaf.class.php
//
// NanoLeaf provides a class to query and manipular Nano Leaf Light Panels and Canvas devices.
//
// Docs:
//
// https://forum.nanoleaf.me/docs/openapi
//
// https://documenter.getpostman.com/view/1559645/RW1gEcCH?version=latest
//
// *********************************************************************************************************************************
//
// Copyright (c) 2019 Mark DeNyse Driftwood Interactive
//
// Permission is hereby granted, free of charge, to any person obtaining a copy
// of this software and associated documentation files (the "Software"), to deal
// in the Software without restriction, including without limitation the rights
// to use, copy, modify, merge, publish, distribute, sublicense, and/or sell
// copies of the Software, and to permit persons to whom the Software is
// furnished to do so, subject to the following conditions:
//
// The above copyright notice and this permission notice shall be included in
// all copies or substantial portions of the Software.
//
// THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
// IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
// FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE
// AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
// LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM,
// OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN THE
// SOFTWARE.
//
// *********************************************************************************************************************************

// Requires fmCURL class which is part of the fmPDA package (driftwoodinteractive.com/fmpda or github.com/driftwoodinteractive/fmPDA)
//require_once('PATH-TO/fmPDA/vLatest/fmCURL.class.php');

class NanoLeaf extends fmCURL
{
   public $host;
   public $port;
   public $authToken;


   function __construct($host, $options = array())
   {
      $this->host = $host;
      $this->port = 16021;

      $this->authToken = array_key_exists('authToken', $options) ? $options['authToken'] : '';

      $options['CURL_CONNECTION_TIMEOUT'] = 5;

      parent::__construct($options);

      return;
   }

   protected function GetBaseURL()
   {
      return $this->host .':'. $this->port .'/api/v1';
   }

   protected function GetAuthURL()
   {
      return $this->GetBaseURL() .'/'. $this->authToken;
   }

   public function curl($url, $method = METHOD_GET, $data = '', $options = array())
   {
      $options['encodeAsJSON']    = true;
      $options['decodeAsJSON']    = true;
      $options['logCURLResult']   = true;

      return parent::curl($url, $method, $data, $options);
   }


   // GetAuthToken()
   //
   // To generate an authorization token:
   // On the Nanoleaf controller, hold the on-off button for 5-7 seconds until the LED starts flashing in a pattern.
   // Call this method within 30 seconds of activating pairing. The token returned is good until the device is reset.
   public function GetAuthToken()
   {
      return $this->curl($this->GetBaseURL(). '/new', METHOD_POST, '');
   }

   public function GetLightControllerInfo()
   {
      return $this->curl($this->GetAuthURL(), METHOD_GET);
   }

   public function Identify()
   {
      return $this->curl($this->GetAuthURL() .'/identify', METHOD_PUT);
   }

   public function GetState()
   {
      return $this->curl($this->GetAuthURL() .'/state/on', METHOD_GET);
   }

   public function GetBrightnesss()
   {
      return $this->curl($this->GetAuthURL() .'/state/brightness', METHOD_GET);
   }

   public function GetColorMode()
   {
      return $this->curl($this->GetAuthURL() .'/state/colorMode', METHOD_GET);
   }

   public function GetSelectedEffect()
   {
      return $this->curl($this->GetAuthURL() .'/effects/select', METHOD_GET);
   }

   public function GetEffectsList()
   {
      return $this->curl($this->GetAuthURL() .'/effects/effectsList', METHOD_GET);
   }

   // Rythm is for Light Panels, not Canvas, although some marketing materials says it's built in to the Panels. Hmmm.
   public function GetIsRhythmConnected()
   {
      return $this->curl($this->GetAuthURL() .'/rhythm/rhythmConnected', METHOD_GET);
   }

   public function GetIsRhythmActive()
   {
      return $this->curl($this->GetAuthURL() .'/rhythm/rhythmActive', METHOD_GET);
   }

   public function GetRhythmID()
   {
      return $this->curl($this->GetAuthURL() .'/rhythm/rhythmId', METHOD_GET);
   }

   public function GetRhythmMode()
   {
      return $this->curl($this->GetAuthURL() .'/rhythm/rhythmMode', METHOD_GET);
   }

   public function GetRhythmPosition()
   {
      return $this->curl($this->GetAuthURL() .'/rhythm/rhythmPos', METHOD_GET);
   }

   public function GetIsAuxAvailable()
   {
      return $this->curl($this->GetAuthURL() .'/rhythm/auxAvailable', METHOD_GET);
   }




   public function SetEffect($effectName)
   {
      return $this->curl($this->GetAuthURL() .'/effects', METHOD_PUT, array('select' => $effectName));
   }


   public function SetState($isOn = true)
   {
      return $this->curl($this->GetAuthURL() .'/state/on', METHOD_PUT, array('on' => array('value' => $isOn)));
   }


   public function SetTemporaryAnimation($effectName, $seconds)
   {
      /*
          {
              command:displayTemp,
              duration: seconds;,
              animName: animation name to set; must exist on controller;
          }
      */

      $data = array('write' =>
                               array(
                                  'command' => 'displayTemp',
                                  'version' => '2.0',
                                  'duration' => intval($seconds),
                                  'animName' => $effectName
                               )
                   );

      return $this->curl($this->GetAuthURL() .'/effects', METHOD_PUT, $data);
   }


   public function SetRandomAnimation($seconds, $options = '')
   {
      // You should include the exact animationOptionJson, but the command must be displayTemp and there must be a duration field.
      // {write : {command : request, animName : Northern Lights}}
      $data = array('write' =>
                               array(
                                  'command' => 'displayTemp',
                                  'version' => '2.0',
                                  'duration' => intval($seconds)
                               )
                   );

      if ($options != '') {
         $data['write']['animation'] = $options;
      }

      return $this->curl($this->GetAuthURL() .'/effects', METHOD_PUT, $data);
   }


}


?>
