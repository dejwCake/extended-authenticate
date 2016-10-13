<?php
namespace DejwCake\ExtendedAuthenticate\Model\Table;

use Cake\ORM\Query;
use Cake\ORM\RulesChecker;
use Cake\ORM\Table;
use Cake\Validation\Validator;

/**
 * UserTokens Model
 *
 * @property \Cake\ORM\Association\BelongsTo $Users
 *
 * @method \DejwCake\ExtendedAuthenticate\Model\Entity\UserToken get($primaryKey, $options = [])
 * @method \DejwCake\ExtendedAuthenticate\Model\Entity\UserToken newEntity($data = null, array $options = [])
 * @method \DejwCake\ExtendedAuthenticate\Model\Entity\UserToken[] newEntities(array $data, array $options = [])
 * @method \DejwCake\ExtendedAuthenticate\Model\Entity\UserToken|bool save(\Cake\Datasource\EntityInterface $entity, $options = [])
 * @method \DejwCake\ExtendedAuthenticate\Model\Entity\UserToken patchEntity(\Cake\Datasource\EntityInterface $entity, array $data, array $options = [])
 * @method \DejwCake\ExtendedAuthenticate\Model\Entity\UserToken[] patchEntities($entities, array $data, array $options = [])
 * @method \DejwCake\ExtendedAuthenticate\Model\Entity\UserToken findOrCreate($search, callable $callback = null)
 *
 * @mixin \Cake\ORM\Behavior\TimestampBehavior
 */
class UserTokensTable extends Table
{

    /**
     * Initialize method
     *
     * @param array $config The configuration for the Table.
     * @return void
     */
    public function initialize(array $config)
    {
        parent::initialize($config);

        $this->table('user_tokens');
        $this->displayField('id');
        $this->primaryKey('id');

        $this->addBehavior('Timestamp');
    }

    /**
     * Default validation rules.
     *
     * @param \Cake\Validation\Validator $validator Validator instance.
     * @return \Cake\Validation\Validator
     */
    public function validationDefault(Validator $validator)
    {
        $validator
            ->integer('id')
            ->allowEmpty('id', 'create');

        $validator
            ->requirePresence('token', 'create')
            ->notEmpty('token')
            ->add('token', 'unique', ['rule' => 'validateUnique', 'provider' => 'table']);

        return $validator;
    }

    /**
     * Returns a rules checker object that will be used for validating
     * application integrity.
     *
     * @param \Cake\ORM\RulesChecker $rules The rules object to be modified.
     * @return \Cake\ORM\RulesChecker
     */
    public function buildRules(RulesChecker $rules)
    {
        $rules->add($rules->isUnique(['token']));

        return $rules;
    }
}
