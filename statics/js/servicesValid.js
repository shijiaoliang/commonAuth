'use strict';
/* servicesValid */
angular.module('app.servicesValid', [])
    .factory('serviceValid', function () {
        return {
            /**
             * 校验是否是邮箱
             * @param email
             * @returns {boolean}
             */
            checkEmail: function (email) {
                var myreg = /^([a-zA-Z0-9]+[_|\_|\.]?)*[a-zA-Z0-9]+@([a-zA-Z0-9]+[_|\_|\.]?)*[a-zA-Z0-9]+\.[a-zA-Z]{2,3}$/;
                if (myreg.test(email)) {
                    return true;
                }
                return false;
            },
            /**
             * 检查输入的一串字符是否全部是数字
             * 输入:str  字符串
             * 返回:true 或 flase; true表示为数字
             */
            checkNum: function (str) {
                var myreg = /^[0-9]*$/;
                if (myreg.test(str)) {
                    return true;
                }
                return false;
            },
        };
    })
;