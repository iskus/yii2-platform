<?php
/**
 * @link https://github.com/gromver/yii2-cmf.git#readme
 * @copyright Copyright (c) Gayazov Roman, 2014
 * @license https://github.com/gromver/yii2-grom/blob/master/LICENSE
 * @package yii2-cmf
 * @version 1.0.0
 */

namespace gromver\platform\console;

use yii\helpers\ArrayHelper;

/**
 * Class Application
 * @package yii2-cmf
 * @author Gayazov Roman <gromver5@gmail.com>
 */
class Application extends \yii\console\Application {
    public $language = 'en';
    public $languages = ['en', 'ru'];
    public $sourceLanguage = 'en';

    private $_modulesHash;

    /**
     * @inheritdoc
     */
    public function __construct($config = [])
    {
        $config = ArrayHelper::merge([
            'components' => [
                'authManager' => [
                    'class' => 'yii\rbac\DbManager',
                    'itemTable' => '{{%grom_auth_item}}',
                    'itemChildTable' => '{{%grom_auth_item_child}}',
                    'assignmentTable' => '{{%grom_auth_assignment}}',
                    'ruleTable' => '{{%grom_auth_rule}}'
                ],
                'cache' => ['class' => 'yii\caching\FileCache'],
                'elasticsearch' => ['class' => 'yii\elasticsearch\Connection'],
                'i18n' => [
                    'translations' => [
                        '*' => [
                            'class' => 'yii\i18n\PhpMessageSource'
                        ],
                    ],
                ],
                // фэйк для обхода BlamableBehavior
                // todo сделать фэйк компонент
                'user' => [
                    'class' => 'StdClass',
                    'isGuest' => false,
                    'id' => 1
                ]
            ],
            'modules' => [
                'grom' => [
                    'class' => 'gromver\platform\console\modules\main\Module'
                ]
            ]
        ], $config);

        $this->_modulesHash = md5(json_encode($config['modules']));

        parent::__construct($config);
    }

    /**
     * @inheritdoc
     */
    public function init()
    {
        $this->bootstrap = array_merge($this->bootstrap, ['grom']);

        parent::init();
    }

    /**
     * @return string
     */
    public function getModulesHash() {
        return $this->_modulesHash;
    }

    /**
     * @return array
     */
    public function getLanguagesList()
    {
        return array_combine($this->languages, $this->languages);
    }

    /**
     * @return \yii\elasticsearch\Connection
     */
    public function getElasticSearch()
    {
        return $this->get('elasticsearch');
    }
}