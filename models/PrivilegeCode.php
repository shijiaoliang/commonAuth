<?php
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

    //菜单检测权限-菜单只检测下属的查询权限
    public static $arrMenuPri = array(
        100 => array(//知识库
            101 => self::PRI_KNOWLEDGE_CATEGORY, //知识库分类管理
            102 => self::PRI_KNOWLEDGE_ADD, //添加知识库文章
            103 => self::PRI_KNOWLEDGE_MODIFY, //知识库文章列表
            104 => self::PRI_KNOWLEDGE_APPRAISE, //知识库-文章评论管理
            105 => self::PRI_KNOWLEDGE_MY, //知识库-我的知识库
            106 => self::PRI_KNOWLEDGE_MY, //知识库-新版知识库
        )
    );
}