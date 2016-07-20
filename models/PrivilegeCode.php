<?php

/**
 * 权限编码所在
 * @author ellan
 */
class PrivilegeCode extends CFormModel {
    //知识库管理组
    const PRI_KNOWLEDGE_CATEGORY = 'KM100101'; //分类管理(拥有知识库分类管理界面)
    const PRI_KNOWLEDGE_ADD = 'KM100102'; //文章添加(拥有知识库文章添加界面，以及预览、保存、保存并提交按钮)
    const PRI_KNOWLEDGE_MODIFY = 'KM100103'; //文章列表查看
    const PRI_KNOWLEDGE_MY = 'KM100104'; //知识库-我的知识库权限
    const PRI_KNOWLEDGE_APPRAISE = 'KM100105'; //知识库-文章评论查询
    const PRI_KNOWLEDGE_DELETE_APPRAISE = 'KM100106'; //知识库-删除评论
    const PRI_KNOWLEDGE_AUDIT = 'KM100107'; //文章编辑权限
    const PRI_KNOWLEDGE_BUILD_INDEX = 'KM100108'; //知识库-维护索引的权限
    const PRI_KNOWLEDGE_PHYSICAL_DELETE = 'KM100109'; //知识库-物理删除

    //海外邀请码
    const PRI_INVITE_CODE_QUERY = 'KM1000201';//海外邀请码查询
    
    //保险
    const PRI_ADD_INSURANCEADD = 'KM100301';//新增线下保险
    const PRI_ADD_INSURANCESEARCH = 'KM100302';//查询线下保险
    const PRI_ADD_ALLINSURANCESEARCH = 'KM100303';//查询全球保险
    const PRI_ADD_INSURANCEUPDATE = 'KM100304';//更新线下保险
    
    //海外网点
    const PRI_SITE_MANAGEMENTE = 'KM100401';//海外RMA查询
    const PRI_REPLACEMENTPART_SEARCH = 'KM100402';//备件申请单查询
    const PRI_REPLACEMENTPART_FUNDSEARCH = 'KM100403';//保证金流水查询
    const PRI_SITE_MANAGEMENTEUPDATE = 'KM100404';//海外RMA操作
    const PRI_REPLACEMENTPART_UPDATE = 'KM100405';//备件申请单操作
    const PRI_RMA_LOGISTICS_MANAGEMENTE = 'KM100406';//RMA物流管理
    
    //zendesk工单
    const PRI_ZENDESK_TICKET_QUERY = 'KM100601';//zendesk工单查询
    
    //海外订单查询
    const PRI_OVERSEA_ORDER_QUERY = 'KM100701';//海外订单查询
    const PRI_OVERSEA_ORDER_ADD = 'KM100703';//海外订单创建
    
    //Mainboard IMEI
    const PRI_SN_IMEIMESSAGE_QUERY = 'KM100702';//Mainboard IMEI
    
    
    
    //网点维护组
    const PRI_BRANCH_SERVICENODE_QUERT = 'AS100001'; //网点维护-网点查询(拥有网点查询界面及其内部按钮)
    const PRI_BRANCH_SERVICENODE_ADD = 'AS100002'; //网点维护-网点新建(拥有网点新建界面及其内部按钮)
    const PRI_BRANCH_SERVICENODE_EDIT = 'AS100003'; //网点维护-网点编辑(拥有网点查询界面的编辑、激活、失效按钮)
    
    //物流管理
    const PRI_Logistics_QUERY = 'KM100500';

    //数据权限
    const PRI_DATARIGHT_STOREID_ID = '22';//知识库的数据权限ID
    const PRI_DATARIGHT_WAREHOUSE_ID = '17';// 仓库的数据权限ID
    const PRI_DATARIGHT_BRANCH_ID = '18';//网点的数据权限ID
    

    //菜单检测权限-菜单只检测下属的查询权限
    public static $arrMenuPri = array(
        100 => array(//知识库
            101 => self::PRI_KNOWLEDGE_CATEGORY, //知识库分类管理
            102 => self::PRI_KNOWLEDGE_ADD, //添加知识库文章
            103 => self::PRI_KNOWLEDGE_MODIFY, //知识库文章列表
            104 => self::PRI_KNOWLEDGE_APPRAISE, //知识库-文章评论管理
            105 => self::PRI_KNOWLEDGE_MY, //知识库-我的知识库
            106 => self::PRI_KNOWLEDGE_MY, //知识库-新版知识库
        ),
        200 => array(//海外邀请码管理
            201 => self::PRI_INVITE_CODE_QUERY, //邀请码列表查询
            202 => self::PRI_INVITE_CODE_QUERY, //邀请码详情查询
        ),
        300 => array(//保险管理
            301 => self::PRI_ADD_INSURANCEADD, //新增保险
            302 => self::PRI_ADD_INSURANCESEARCH, //查询保险
            303 => self::PRI_ADD_ALLINSURANCESEARCH, //全球保险  
        ),
        400 => array(//海外网点管理
            401 => self::PRI_SITE_MANAGEMENTE, //海外网点rma管理
            402 => self::PRI_REPLACEMENTPART_SEARCH, //海外网点备件管理
            403 => self::PRI_REPLACEMENTPART_FUNDSEARCH, //海外网点保证金管理
            404 => self::PRI_RMA_LOGISTICS_MANAGEMENTE,//RMA运单管理
        ),
        500 => array(//物流管理
            501 => self::PRI_Logistics_QUERY, //物流查询
            502 => self::PRI_Logistics_QUERY, //运单列表
        ),
        600 => array(//zendesk
            601 => self::PRI_ZENDESK_TICKET_QUERY, //zendesk
            602 => self::PRI_ZENDESK_TICKET_QUERY, //zendesk
        ),
        700 => array(
            701 => self::PRI_OVERSEA_ORDER_QUERY, 
            702 => self::PRI_SN_IMEIMESSAGE_QUERY,
            703 => self::PRI_OVERSEA_ORDER_ADD,
        ),
    );
}