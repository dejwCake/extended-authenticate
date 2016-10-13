<?php
namespace DejwCake\ExtendedAuthenticate\Test\TestCase\Model\Table;

use Cake\ORM\TableRegistry;
use Cake\TestSuite\TestCase;
use DejwCake\ExtendedAuthenticate\Model\Table\UserTokensTable;

/**
 * DejwCake\ExtendedAuthenticate\Model\Table\UserTokensTable Test Case
 */
class UserTokensTableTest extends TestCase
{

    /**
     * Test subject
     *
     * @var \DejwCake\ExtendedAuthenticate\Model\Table\UserTokensTable
     */
    public $UserTokens;

    /**
     * Fixtures
     *
     * @var array
     */
    public $fixtures = [
        'plugin.dejw_cake/extended_authenticate.user_tokens',
        'plugin.dejw_cake/extended_authenticate.users'
    ];

    /**
     * setUp method
     *
     * @return void
     */
    public function setUp()
    {
        parent::setUp();
        $config = TableRegistry::exists('UserTokens') ? [] : ['className' => 'DejwCake\ExtendedAuthenticate\Model\Table\UserTokensTable'];
        $this->UserTokens = TableRegistry::get('UserTokens', $config);
    }

    /**
     * tearDown method
     *
     * @return void
     */
    public function tearDown()
    {
        unset($this->UserTokens);

        parent::tearDown();
    }

    /**
     * Test initialize method
     *
     * @return void
     */
    public function testInitialize()
    {
        $this->markTestIncomplete('Not implemented yet.');
    }

    /**
     * Test validationDefault method
     *
     * @return void
     */
    public function testValidationDefault()
    {
        $this->markTestIncomplete('Not implemented yet.');
    }

    /**
     * Test buildRules method
     *
     * @return void
     */
    public function testBuildRules()
    {
        $this->markTestIncomplete('Not implemented yet.');
    }
}
