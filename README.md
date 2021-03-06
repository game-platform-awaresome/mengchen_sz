# 梦晨游戏管理后台
## 环境依赖
- php >= 5.6  
- nginx打开ssi  
- redis >= 2.8
- composer  
- supervisor  

```
supervisor配置文件模版：
[program:laravel-queue-worker]
process_name=%(program_name)s_%(process_num)02d
directory=/data/www/mengchen_new_sz   ;低版本不支持此指令
command=/usr/bin/php /data/www/mengchen_new_sz/artisan queue:work --delay=3 --sleep=1 --tries=3 --timeout=60
autostart=true
autorestart=true
startretries=3
user=nginx
numprocs=1
redirect_stderr=true
stdout_logfile=/data/log/supervisor/%(program_name)s.log
stdout_logfile_maxbytes=100MB
stdout_logfile_backups=10

容器环境模版：
[program:laravel-queue-worker]
#process_name=%(program_name)s_%(process_num)02d
#directory=
command=/usr/local/bin/docker-compose -f /data/docker/docker-compose.yml exec -T php /bin/bash -c "runuser -u www-data -- php /data/www/mengchen_sz/artisan queue:work --delay=3 --sleep=1 --tries=3 --timeout=60"
autostart=true
autorestart=true
startretries=3
user=root
#numprocs=1
redirect_stderr=true
stdout_logfile=/data/log/supervisor/%(program_name)s.log
stdout_logfile_maxbytes=100MB
stdout_logfile_backups=10
```  
- node & npm

```
安装node和npm环境：
curl --silent --location https://rpm.nodesource.com/setup_8.x | sudo bash -
yum -y install nodejs
```

## 单元测试与代码覆盖率
测试并生成代码覆盖率报表：  
```
cd ${code_ducument_root}
./vendor/bin/phpunit --coverage-html public/test/
```
查看代码覆盖率：
```
URI: /test/index.html
```

## 生产环境代码发布  

```
cd ${code_ducument_root}
git pull                #获取最新代码
composer install        #安装laravle依赖
cp .env.example .env    #配置文件(根据生产环境配置对应的参数)
php artisan migrate     #创建表(database要提前创建)
ln -sv ../storage/app/public/ public/storage    #创建符号链接到文件上传目录
chmod -R {phpfpm_runner}.{phpfpm_runner} ./ #更改代码目录的权限为phpfpm程序的运行用户
chmod +x vendor/phpunit/phpunit/phpunit #添加执行权限
./vendor/bin/phpunit    #代码测试

cd client       #进入js开发目录
npm install     #安装npm包
npm run build   #编译js代码
```  

### cron计划任务
```
crontab -e
* * * * * sudo -u nginx /usr/bin/php /data/www/{code_ducument_root}/artisan schedule:run >> /dev/null 2>&1  

#注意：.env里面正确配置好日志输出文件"CRON_TASK_LOG"

运行容器时命令：
* * * * * /usr/local/bin/docker-compose -f /data/docker/docker-compose.yml exec -T php /bin/bash -c "runuser -u www-data php /data/www/mengchen_sz/artisan schedule:run" >> /dev/null 2>&1
```  

**任务列表**  

| 任务名   | 运行时间  | 描述 |     
| ----  | :-----: | ----------: |
| admin:generate-daily-statement | 每日00:00 | 统计上一天的数据报表并入库 |
| admin:fetch-online-player-count | 每10分钟 | 统计在线和游戏中玩家数并入库 |
| admin:cache-agent-valid-card-log | 每10分钟 | 缓存包含了有效耗卡数据的代理商给玩家充值记录的缓存 |
| admin:calc-wx-order-rebate | 每月1号 1:00 | 计算上一个月的微信订单返利并入库 |

### 使用post-merge钩子脚本  
```
#!/bin/sh

codeDir=$(cd $(dirname $0); pwd)'/../../'
queueName="laravel-queue-worker"

supervisorctl restart $queueName #重启队列

cd $codeDir
composer install
#docker环境
#/usr/local/bin/docker-compose -f /data/docker/docker-compose.yml exec -T php /bin/bash -c "cd ${codeDir}; composer install"

cd client
npm install
npm run build
```

## 开发环境规范
### 开发环境使用pre-push钩子
```
#!/bin/sh

codeDir=$(cd $(dirname $0); pwd)'/../../'
cd $codeDir
./vendor/bin/phpunit
```

## 后端接口列表
### 管理员接口
> **前缀/admin/api/**

| URI   | Method  | Description |     
| ----  | :-----: | ----------: |
| self/password | PUT | 更新密码 |
| game/player | GET | 玩家列表 |
| statement/summary | GET | 获取数据报表总览 |
| statement/summary/excel | GET | 导出数据报表总览为excel |
| statement/real-time | GET | 获取实时报表数据 |
| statement/online-players | GET | 获取在线玩家图表数据 |
| statement/room | GET | 获取开房数据报表数据 |
| gm/records | GET | 根据玩家id战绩其查询 |
| gm/record-info/{recId} | GET | 根据战绩id查询战绩详情 |
| gm/room | GET | 获取可创建的房间类型 |
| gm/room | POST | 创建游戏房间 |
| gm/room/open | GET | 获取正在玩的房间 |
| gm/room/history | GET | 获取房间历史 |
| gm/marquee | GET | 跑马灯列表 |
| gm/marquee | POST | 添加跑马灯 |
| gm/marquee/{id} | PUT | 修改跑马灯 |
| gm/marquee/{id} | DELETE | 删除跑马灯 |
| activities/list | GET | 获取活动列表 |
| activities/list | PUT | 编辑活动列表 |
| activities/list/{aid} | DELETE | 删除活动列表 |
| activities/list | POST | 添加活动列表 |
| activities/reward-map | GET | 获取活动奖励id和name的map |
| activities/reward | GET | 获取奖品列表 |
| activities/reward | PUT | 编辑奖品 |
| activities/reward/{pid} | DELETE | 删除奖品 |
| activities/reward | POST | 添加奖品 |
| activities/goods-type | GET | 获取活动奖励道具列表 |
| activities/goods-type | PUT | 编辑活动奖励道具 |
| activities/goods-type/{goodsId} | DELETE | 删除活动奖励道具 |
| activities/goods-type | POST | 添加活动奖励道具 |
| activities/goods-type-map | GET | 获取活动奖励道具列表映射 |
| activities/task | GET | 获取任务列表 |
| activities/task | PUT | 编辑任务 |
| activities/task/{taskId} | DELETE | 删除任务 |
| activities/task | POST | 添加任务 |
| activities/task-type-map | GET | 获取任务类型映射 |
| activities/task-map | GET | 获取任务id和name的映射 |
| activities/user-goods | GET | 获取玩家物品列表 |
| activities/user-goods | PUT | 编辑玩家物品 |
| activities/user-goods | DELETE | 删除玩家物品 |
| activities/user-goods | POST | 添加玩家物品 |
| activities/user-goods/reset | PUT | 重置玩家物品 |
| activities/tasks-player | GET | 获取玩家任务列表 |
| activities/tasks-player | PUT | 编辑玩家任务 |
| activities/tasks-player | DELETE | 删除玩家任务 |
| activities/tasks-player | POST | 添加玩家任务 |
| activities/tasks-player/reset | PUT | 重置玩家任务 |
| activities/red-packet-log | GET | 活动红包发送记录 |
| activities/red-packet-log/status/{redPacketLog} | PUT | 更新活动红包发送状态 |
| activities/statement | GET | 活动统计数据 |
| activities/log-activity-reward | GET | 玩家中奖记录 |
| community | GET | 获取牌艺馆列表 |
| community | POST | 创建牌艺馆 |
| community/{community} | DELETE | 删除牌艺馆 |
| community/audit/{community} | POST | 审核牌艺馆创建申请 |
| community/valid-card-consumed | GET | 获取牌艺馆有效耗卡数据 |
| community/game-group/id-name-map | GET | 获取游戏组id和分组名字的映射表 |
| stock | POST | 申请库存 |
| stock/list | GET | 库存申请列表 |
| stock/history | GET | 库存审批记录 |
| stock/approval/{id} | POST | 审批通过 |
| stock/decline/{id} | POST | 审批拒绝 |
| agent | GET | 代理商列表 |
| agent | POST | 新建代理商 |
| agent/{id} | DELETE | 删除代理商 |
| agent/{id} | PUT | 更新代理商信息 |
| agent/pass/{id} | PUT | 更新代理商密码 |
| agent/bills | GET | 查询代理商的售卡记录 |
| agent/card/valid-consumed-list | GET | 代理商有效耗卡记录 |
| top-up/admin | GET | 管理员给代理商充值记录 |
| top-up/agent | GET | 代理商给下级代理商充值记录 |
| top-up/player | GET | 给玩家的充值记录 |
| top-up/agent/{receiver}/{type}/{amount} | POST | 给代理商充值 |
| top-up/player/{player}/{type}/{amount} | POST | 给玩家充值 |
| group/authorization/view/{group} | GET | 获取某个组可以访问的视图(group=0表示当前登录用户) |   
| group/authorization/view/{group} | PUT | 设置组权限(可访问的视图页面) |   
| group | GET | 列出所有组(除了代理商) |
| group | POST | 创建组 |
| group/{group} | PUT | 更新组信息(名字) |
| group/{group} | DELETE | 删除组 |
| group/map | GET | 获取组id与名字的映射关系 |
| role | GET | 列出所有角色(除了代理商) |
| role | POST | 创建角色 |
| role/{role} | PUT | 更新角色信息 |
| role/{role} | DELETE | 删除角色 |
| system/log | GET | 系统操作日志记录 | 
| wx-top-up-rules | GET | 充值套餐规则列表 |
| wx-top-up-rules | POST | 添加充值套餐规则 |
| wx-top-up-rules/{rule} | PUT | 修改充值套餐规则 |
| wx-top-up-rules/{rule} | DELETE | 删除充值套餐规则 |
| rebate-rules | GET | 返利规则列表 |
| rebate-rules | POST | 添加返利规则 |
| rebate-rules/{rule} | PUT | 修改返利规则 |
| rebate-rules/{rule} | DELETE | 删除返利规则 |
| withdrawals | GET | 审核提现列表 |
| withdrawals/audit/{withdrawal} | POST | 审核提现 |
| rebates/user/{user?} | GET | 查看代理商返利 |
| rebates/user/statistics/{user} | GET | 查看代理商返利统计 |



### 代理商接口
> **前缀/agent/api/**  

| URI   | Method  | Description |     
| ----  | :-----: | ----------: |
| self/info | PUT | 更新个人信息 |
| self/password | PUT | 更新个人密码 |
| self/agent-type | GET | 获取代理商代理级别 |
| stock | POST | 申请库存 |
| stock/history | GET | 库存申请记录 |
| subagent | GET | 子代理商列表 |
| subagent | POST | 创建子代理商 |
| subagent/{id} | DELETE | 删除子代理商 |
| subagent/{id} | PUT | 更新子代理商信息(包括密码) |
| community | GET | 获取牌艺馆列表 |
| communities | GET | 获取代理商已审核过的牌艺馆(无分页) |
| community | POST | 新增牌艺馆 |
| community/{communityId} | DELETE | 删除牌艺馆(已关闭) |
| community/info/{communityId} | GET | 获取单个牌艺馆基本信息 |
| community/detail/{communityId} | GET | 获取牌艺馆详细信息 |
| community/info/{community} | PUT | 更新牌艺馆信息 |
| community/card/top-up-history | GET | 牌艺馆充值记录 |
| community/card/top-up | POST | 充值牌艺馆房卡 |
| community/member/invitation | POST | 邀请玩家加入牌艺馆 |
| community/member/approval-application/{application} | PUT | 同意玩家入馆申请 |
| community/member/decline-application/{application} | PUT | 拒绝玩家入馆申请 |
| community/member/kick-out | PUT | 从牌艺馆中踢出成员 |
| community/member/log/read| PUT | 标记社区动态为已读 |
| community/room/{communityId} | GET | 获取牌艺馆房间(正在玩) |
| community/game-record/{communityId} | GET | 查询社区玩家战绩 |
| community/game-record/mark/{recordInfoId} | PUT | 标记战绩为已读 |
| community/game-type/rules/{community} | GET | 获取牌艺馆所关联的游戏包的所有游戏的游戏规则 |
| community/game-type/rule/{community} | POST | 创建/编辑牌艺馆游戏默认规则 |
| community/game-type/rule | GET | 获取某个牌艺馆的某个游戏类型的游戏规则模版 |
| top-up/child/{receiver}/{type}/{amount} | POST | 给子代理商充值 |
| top-up/player/{player}/{type}/{amount} | POST | 给玩家充值 |
| top-up/child | GET | 给自代理商的充值记录 |
| top-up/player | GET | 给玩家的充值记录 |
| rebates | GET | 返利列表 |
| rebates/statistics | GET | 返利统计 |
| withdrawals | GET | 提现列表 |
| withdrawals/amount-limit | GET | 提现金额限制 |
| withdrawals | POST | 提现申请 |
| wx-top-up-rules | GET | 充值套餐规则列表 |

### 公共接口
| URI   | Method  | Description |     
| ----  | :-----: | ----------: |
| /api/info | GET | 获取用户个人信息 |
| /api/content-header-h1 | GET | 获取页面的H1标题内容|
| /api/game/room/type-map | GET | 获取游戏后端游戏类型id的映射关系 |
| /api/game/player | GET | 查找玩家 |

### 微信回调接口
| URI   | Method  | Description |     
| ----  | :-----: | ----------: |
| /wechat/official-account/callback | ANY | 微信公众号事件回调 |
| /wechat/official-account/authorization | ANY | 微信公众号网页授权回调(使用路由，此回调暂未启用) |

### 微信订单接口 
> **前缀/api/wechat**  

| URI   | Method  | Description |     
| ----  | :-----: | ----------: |
| order | GET | 管理员查看订单列表 |
| order/agent | GET | 代理商查看订单列表 |
| order/agent/{order} | GET | 代理商查看订单详情 |
| order | POST | 创建微信订单 |
| order/notification | ANY | 微信通知回调 |

