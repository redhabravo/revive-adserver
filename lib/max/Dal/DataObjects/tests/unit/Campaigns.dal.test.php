<?php
/*
+---------------------------------------------------------------------------+
| Max Media Manager v0.3                                                    |
| =================                                                         |
|                                                                           |
| Copyright (c) 2003-2006 m3 Media Services Limited                         |
| For contact details, see: http://www.m3.net/                              |
|                                                                           |
| This program is free software; you can redistribute it and/or modify      |
| it under the terms of the GNU General Public License as published by      |
| the Free Software Foundation; either version 2 of the License, or         |
| (at your option) any later version.                                       |
|                                                                           |
| This program is distributed in the hope that it will be useful,           |
| but WITHOUT ANY WARRANTY; without even the implied warranty of            |
| MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the             |
| GNU General Public License for more details.                              |
|                                                                           |
| You should have received a copy of the GNU General Public License         |
| along with this program; if not, write to the Free Software               |
| Foundation, Inc., 59 Temple Place, Suite 330, Boston, MA  02111-1307  USA |
+---------------------------------------------------------------------------+
$Id$
*/

require_once MAX_PATH . '/lib/max/Dal/tests/util/DalUnitTestCase.php';

/**
 * A class for testing non standard DataObjects_Campaigns methods
 *
 * @package    MaxDal
 * @subpackage TestSuite
 *
 */
class DataObjects_CampaignsTest extends DalUnitTestCase
{
    /**
     * The constructor method.
     */
    function DataObjects_CampaignsTest()
    {
        $this->UnitTestCase();
    }
    
    function tearDown()
    {
        DataGenerator::cleanUp();
    }
    
    function testInsert()
    {
        // Prepare test data
        $clientId = DataGenerator::generateOne('clients');
        
        $doTrackers = MAX_DB::factoryDO('trackers');
        $doTrackers->clientid = $clientId;
        $doTrackers->linkcampaigns = 't';
        $aTrackerId = DataGenerator::generate($doTrackers, $numTrackers = 2);
        
        $doTrackers = MAX_DB::factoryDO('trackers');
        $doTrackers->linkcampaigns = 'f';
        DataGenerator::generateOne($doTrackers); // redundant one

        // Test that inserting new campaigns triggers to insert new campaigns_trackers (if exists)
        $doCampaigns = MAX_DB::factoryDO('campaigns');
        $doCampaigns->clientid = $clientId;
        $campaignId = $doCampaigns->insert();
        $this->assertNotEmpty($campaignId);
        
        // Test that two campaign_trackers were inserted as well
        $doCampaigns_trackers = MAX_DB::factoryDO('campaigns_trackers');
        $doCampaigns_trackers->campaignid = $campaignId;
        $this->assertEqual($doCampaigns_trackers->count(), $numTrackers);
        
        // Delete any data which wasn't created by DataGenerator
        DataGenerator::cleanUp(array('campaigns', 'campaigns_trackers'));
    }
    
}
?>