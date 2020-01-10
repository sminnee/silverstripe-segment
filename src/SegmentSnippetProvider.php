<?php

namespace Sminnee\SilverstripeSegment;

use SilverStripe\TagManager\SnippetProvider;
use SilverStripe\Forms\FieldList;
use SilverStripe\Forms;
use SilverStripe\Security\Security;

/**
 * A snippet provider that lets you add arbitrary HTML
 */
class SegmentSnippetProvider implements SnippetProvider
{

    public function getTitle()
    {
        return "Segment.com";
    }

    public function getParamFields()
    {

        return new FieldList(
            (new Forms\TextField("ApiKey", "Segment.com API key"))
                ->setDescription("Create a JavaScript Source in segment.com and in the segment code, look for the line 'analytics.load(\"xxxx\");'. Copy The xxxx text."),
                (new Forms\CheckboxField("SendMemberData", "Send details of logged-in user"))
                ->setDescription("If checked, the name and email address of the currently logged in user will be sent to segment")
        );
    }

    public function getSummary(array $params)
    {
        if (!empty($params['ApiKey'])) {
            return $this->getTitle() . " -  " . $params['ApiKey'];
        } else {
            return $this->getTitle();
        }
    }

    public function getSnippets(array $params)
    {
        if (empty($params['ApiKey'])) {
            throw new \InvalidArgumentException("Please supply your API Key");
        }

        // Sanitise the ID
        $apiKey = trim(preg_replace('[^A-Za-z0-9_\-]', '', $params['ApiKey']));

        $extraScript = "";

        // Add logged-in user info
        if (!empty($params['SendMemberData']) && $user = Security::getCurrentUser()) {
            $memberData = [
                'email' => $user->Email,
                'firstName' => $user->FirstName,
                'lastName' => $user->Surname,
            ];
            $memberIDJSON = json_encode($user->ID);
            $memberDataJSON = json_encode($memberData);

            $extraScript .= "\n" . <<<HTML
<script>
    analytics.ready(function() {
        analytics.identify($memberIDJSON, $memberDataJSON);
    });
</script>
HTML;
        }

        $snippet = <<<HTML
<script>
  !function(){var analytics=window.analytics=window.analytics||[];if(!analytics.initialize)if(analytics.invoked)window.console&&console.error&&console.error("Segment snippet included twice.");else{analytics.invoked=!0;analytics.methods=["trackSubmit","trackClick","trackLink","trackForm","pageview","identify","reset","group","track","ready","alias","debug","page","once","off","on"];analytics.factory=function(t){return function(){var e=Array.prototype.slice.call(arguments);e.unshift(t);analytics.push(e);return analytics}};for(var t=0;t<analytics.methods.length;t++){var e=analytics.methods[t];analytics[e]=analytics.factory(e)}analytics.load=function(t,e){var n=document.createElement("script");n.type="text/javascript";n.async=!0;n.src="https://cdn.segment.com/analytics.js/v1/"+t+"/analytics.min.js";var a=document.getElementsByTagName("script")[0];a.parentNode.insertBefore(n,a);analytics._loadOptions=e};analytics.SNIPPET_VERSION="4.1.0";
  analytics.load("$apiKey");
  analytics.page();
  }}();
</script>$extraScript
HTML;

        return [
            self::ZONE_HEAD_END => $snippet,
        ];
    }
}
