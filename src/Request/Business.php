<?php

namespace InstagramNextAPI\Request;

use InstagramNextAPI\Constants;
use InstagramNextAPI\Response;

/**
 * Business-account related functions.
 *
 * These only work if you have a Business account.
 */
class Business extends RequestCollection
{
    /**
     * Get insights.
     *
     * @param $day
     *
     * @throws \InstagramNextAPI\Exception\InstagramException
     *
     * @return \InstagramNextAPI\Response\InsightsResponse
     */
    public function getInsights(
        $day = null)
    {
        if (empty($day)) {
            $day = date('d');
        }

        return $this->ig->request('insights/account_organic_insights/')
            ->addParam('show_promotions_in_landing_page', 'true')
            ->addParam('first', $day)
            ->getResponse(new Response\InsightsResponse());
    }

    /**
     * Get media insights.
     *
     * @param string $mediaId The media ID in Instagram's internal format (ie "3482384834_43294").
     *
     * @throws \InstagramNextAPI\Exception\InstagramException
     *
     * @return \InstagramNextAPI\Response\MediaInsightsResponse
     */
    public function getMediaInsights(
        $mediaId)
    {
        return $this->ig->request("insights/media_organic_insights/{$mediaId}/")
            ->addParam('ig_sig_key_version', Constants::SIG_KEY_VERSION)
            ->getResponse(new Response\MediaInsightsResponse());
    }

    /**
     * Get account statistics.
     *
     * @throws \InstagramNextAPI\Exception\InstagramException
     *
     * @return \InstagramNextAPI\Response\GraphqlResponse
     */
    public function getStatistics()
    {
        return $this->ig->request('ads/graphql/')
            ->setSignedPost(false)
            ->setIsMultiResponse(true)
            ->addParam('locale', Constants::LOCALE)
            ->addParam('vc_policy', 'insights_policy')
            ->addParam('surface', 'account')
            ->addPost('access_token', 'undefined')
            ->addPost('fb_api_caller_class', 'RelayModern')
            ->addPost('variables', json_encode([
                'IgInsightsGridMediaImage_SIZE' => 240,
                'timezone'                      => 'Atlantic/Canary',
                'activityTab'                   => true,
                'audienceTab'                   => true,
                'contentTab'                    => true,
                'query_params'                  => json_encode([
                    'access_token'  => '',
                    'id'            => $this->ig->account_id,
                ]),
            ]))
            ->addPost('doc_id', '1926322010754880')
            ->getResponse(new Response\GraphqlResponse());
    }
}
