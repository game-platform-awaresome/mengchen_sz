<?php
namespace App\Traits;

trait GameRulesMap
{
    public function __get($name)
    {
        if ($name === 'gameRules') {
            return $this->getGameRules();
        } else {
            $trace = debug_backtrace();
            trigger_error( 'Undefined property via __get(): ' . $name .
                ' in ' . $trace[0]['file'] .
                ' on line ' . $trace[0]['line'],
                E_USER_NOTICE);
            return null;
        }
    }

    private function getGameRules()
    {
        return [
            257 => $this->majiangCommonRules,
            258 => $this->majiangCommonRules,
            259 => $this->majiangCommonRules,
            260 => $this->majiangCommonRules,
            261 => $this->majiangCommonRules,
            262 => $this->majiangCommonRules,
            263 => $this->majiangCommonRules,
            264 => $this->majiangCommonRules,
            265 => $this->majiangCommonRules,
            266 => $this->majiangCommonRules,
            267 => $this->majiangCommonRules,
        ];
    }

    //麻将的通用options
    private $majiangCommonRules = [
//        1 => '房间类型',  //目前只有广东麻将(kind4)，表示玩法类型(清远庄，惠州庄等)
//        2 => '局数',
//        3 => '人数',
//        4 ＝> '社团id'，

        'room' => [
            'key' => 1,
            'name' => '房间类型',    //即麻将玩法
        ],

        'rounds' => [
            'key' => 2,
            'name' => '局数',
            'choices' => [
                8 => '8局',
                16 => '16局',
            ]
        ],

        'players' => [
            'key' => 3,
            'name' => '人数',
        ],

        'wanfa' => [
            'name' => '玩法',
            'options' => [
                //通用
                10 => '抢杠',
                11 => '抢杠全包',
                12 => '流局算杠',
                13 => '吃牌',
                14 => '七小对胡',
                15 => '鸡胡',
                18 => '截胡',
                19 => '天地胡',

                20 => '长毛',
                21 => '包牌',
                22 => '小讨',
                23 => '暗杠可摆',
                24 => '庄家翻倍',
                25 => '带飘',
                //26 => '底分',
                27 => '鸡胡点炮',
                28 => '抢明杠',

                //广东推到胡
                30 => '无字牌',
                31 => '七对翻倍',
                32 => '跟庄',
                33 => '杠爆全包',
                34 => '无鬼加倍',
                35 => '节节高',
                36 => '12张落地',
                //37 => '鬼牌类型(1翻鬼(随机1个鬼),2白板鬼,3双鬼(随机2个鬼),4无鬼)',

                //40 => '没有房主标示',
                //41 => '推广帐号uid(php 创建房间才有,默认为0)'

                50 => '缺万字',
                51 => '4鬼胡牌2倍',

                //潮汕
                52 => '罚马',
                53 => '买马',
                54 => '马跟杠',    //（中马对杠分有效，中马后手中杠分也跟着加倍。）
                55 => '接炮胡',
                56 => '十三幺特殊',  //十三幺听牌时，无论选项是否勾选了可接炮胡，都可以接炮胡）
                57 => '漏碰', //可以碰时选过同一圈不能再碰同一张牌
                58 => '漏杠',   //可以杠时选过，一局都不能再杠这张牌，不影响暗杠）
                59 => '漏胡', //过胡不胡
                60 => '海底捞月',   //除去了预留的马牌数量，摸到最后一张牌胡牌则算海底捞月，胡牌分翻一倍
                61 => '杠上花',   //(杠爆）胡牌分翻倍（杠分、跟庄分、连庄分不翻倍）
                62 => '人胡',
                63 => '4鬼胡',

                //万年
                //64 => '清混/硬胡',  // (0为硬胡，1为清混)
                //65 => '分数封顶(0为不封顶,1为10分封顶,2为15分封顶,3为20分封顶)',

                500 => '一炮多响',
            ],
        ],

        'hua_pai' => [
            'key' => 16,
            'name' => '花牌类型',
            'choices' => [
                0 => '无鬼补花',
                35 => '花牌做鬼',
            ],
        ],

        'gui_pai' => [
            'key' => 37,
            'name' => '鬼牌类型',
            'choices' => [
                1 => '翻鬼',
                2 => '白板鬼',
                3 => '双鬼',
                4 => '无鬼',
            ],
        ],

        'ma_pai' => [
            'key' => 17,
            'name' => '抓马个数',
            'choices' => [
                0 => '无马',
                2 => '买2马',
                4 => '买4马',
                6 => '买6马',
                8 => '买8马',
                10 => '买10马',
            ]
        ],

        'di_fen' => [
            'key' => 26,
            'name' => '底分',
        ],

        'qing_hun' => [
            'key' => 64,
            'name' => '清混',
            'choices' => [
                0 => '清混',
                1 => '硬胡',
            ],
        ],

        'score_limit' => [
            'key' => 65,
            'name' => '分数封顶',
            'choices' => [
                0 => '不封顶',
                1 => '10分封顶',
                2 => '15分封顶',
                3 => '20分封顶',
            ],
        ],
    ];
}