import 'jquery-validation';

$.validator.setDefaults({
  errorClass: 'form-error-message jq-validate-error',
  errorElement: 'p',
  onkeyup: false,
  ignore: '',
  ajax: false,
  currentDom: null,
  highlight: function (element, errorClass, validClass) {
    let $row = $(element).addClass('form-control-error').closest('.form-group').addClass('has-error');
    $row.find('.help-block').hide();
  },
  unhighlight: function (element, errorClass, validClass) {
    let $row = $(element).removeClass('form-control-error').closest('.form-group');
    $row.removeClass('has-error');
    $row.find('.help-block').show();
  },
  errorPlacement: function (error, element) {
    if (element.parent().hasClass('controls')) {
      element.parent('.controls').append(error);
    } else if (element.parent().hasClass('input-group')) {
      element.parent().after(error);
    } else if (element.parent().is('label')) {
      element.parent().parent().append(error);
    } else {
      element.parent().append(error);
    }
  },
  submitError: function () {
    console.log('submitError');
  },
  submitSuccess: function (data) {
    console.log('submitSuccess');
  },
  submitHandler: function (form) {
    console.log('submitHandler');

    //规定全局不要用 submit按钮（<input type=’submit’>）提交表单；
    let $form = $(form);
    let settings = this.settings;
    $(settings.currentDom) ? $(settings.currentDom).button('loading') : '';
    if (settings.ajax) {
      $.post($form.attr('action'), $form.serializeArray(), (data) => {
        settings.submitSuccess(data);
      }).error(() => {
        settings.currentDom ? settings.currentDom.button('reset') : '';
        settings.submitError();
      });
    } else {
      form.submit();
    }
  }
});

$.extend($.validator.prototype, {
  defaultMessage: function (element, rule) {
    if (typeof rule === "string") {
      rule = { method: rule };
    }

    var message = this.findDefined(
      this.customMessage(element.name, rule.method),
      this.customDataMessage(element, rule.method),

      // 'title' is never undefined, so handle empty string as undefined
      !this.settings.ignoreTitle && element.title || undefined,
      $.validator.messages[rule.method],
      "<strong>Warning: No message defined for " + element.name + "</strong>"
    ),
      theregex = /\$?\{(\d+)\}/g,
      displayregex = /%display%/g;
    if (typeof message === "function") {
      message = message.call(this, rule.parameters, element);
    } else if (theregex.test(message)) {
      message = $.validator.format(message.replace(theregex, "{$1}"), rule.parameters);
    }

    if (displayregex.test(message)) {
      var labeltext, name;
      var id = $(element).attr("id");
      if (id) {
        labeltext = $("label[for=" + id + "]").text();
        if (labeltext) {
          labeltext = labeltext.replace(/^[\*\s\:\：]*/, "").replace(/[\*\s\:\：]*$/, "");
        }
      }

      name = $(element).attr("name");
      message = message.replace(displayregex, labeltext || name)
    }

    return message;
  }

});


$.extend($.validator.messages, {
  required: "请输入%display%",
  remote: "请修正此字段",
  email: "请输入有效的电子邮件地址",
  url: "请输入有效的网址",
  date: "请输入有效的日期",
  dateISO: "请输入有效的日期 (YYYY-MM-DD)",
  number: "请输入有效的数字",
  digits: "只能输入整数",
  creditcard: "请输入有效的信用卡号码",
  equalTo: "你的输入不相同",
  extension: "请输入有效的后缀",
  maxlength: $.validator.format("最多可以输入 {0} 个字符"),
  minlength: $.validator.format("最少要输入 {0} 个字符"),
  rangelength: $.validator.format("请输入长度在 {0} 到 {1} 之间的字符串"),
  range: $.validator.format("请输入范围在 {0} 到 {1} 之间的数值"),
  max: $.validator.format("请输入不大于 {0} 的数值"),
  min: $.validator.format("请输入不小于 {0} 的数值")
});

$.validator.addMethod("DateAndTime", function (value, element) {
  let reg = /^(?:(?!0000)[0-9]{4}-(?:(?:0[1-9]|1[0-2])-(?:0[1-9]|1[0-9]|2[0-8])|(?:0[13-9]|1[0-2])-(?:29|30)|(?:0[13578]|1[02])-31)|(?:[0-9]{2}(?:0[48]|[2468][048]|[13579][26])|(?:0[48]|[2468][048]|[13579][26])00)-02-29) ([0-1]{1}[0-9]{1})|(2[0-4]{1}):[0-5]{1}[0-9]{1}$/;
  return this.optional(element) || reg.test(value);
}, $.validator.format("请输入正确的日期和时间,格式如XXXX-MM-DD hh:mm"));

$.validator.addMethod("trim", function (value, element, params) {
  return $.trim(value).length > 0;
}, jQuery.validator.format("请输入%display%"));

$.validator.addMethod("idcardNumber", function (value, element, params) {
  let _check = function (idcardNumber) {
    let reg = /^\d{17}[0-9xX]$/i;
    if (!reg.test(idcardNumber)) {
      return false;
    }
    let n = new Date();
    let y = n.getFullYear();
    if (parseInt(idcardNumber.substr(6, 4)) < 1900 || parseInt(idcardNumber.substr(6, 4)) > y) {
      return false;
    }
    let birth = idcardNumber.substr(6, 4) + "-" + idcardNumber.substr(10, 2) + "-" + idcardNumber.substr(12, 2);
    if (!'undefined' == typeof birth.getDate) {
      return false;
    }
    let IW = [7, 9, 10, 5, 8, 4, 2, 1, 6, 3, 7, 9, 10, 5, 8, 4, 2, 1];
    let iSum = 0;
    for (let i = 0; i < 17; i++) {
      iSum += parseInt(idcardNumber.charAt(i)) * IW[i];
    }
    let iJYM = iSum % 11;
    let sJYM = ''
    if (iJYM == 0) sJYM = '1';
    else if (iJYM == 1) sJYM = '0';
    else if (iJYM == 2) sJYM = 'x';
    else if (iJYM == 3) sJYM = '9';
    else if (iJYM == 4) sJYM = '8';
    else if (iJYM == 5) sJYM = '7';
    else if (iJYM == 6) sJYM = '6';
    else if (iJYM == 7) sJYM = '5';
    else if (iJYM == 8) sJYM = '4';
    else if (iJYM == 9) sJYM = '3';
    else if (iJYM == 10) sJYM = '2';
    let cCheck = idcardNumber.charAt(17).toLowerCase();
    if (cCheck != sJYM) {
      return false;
    }
    return true;
  }
  return this.optional(element) || _check(value);
}, "请正确输入您的身份证号码");

$.validator.addMethod("visible_character", function (value, element, params) {
  return this.optional(element) || $.trim(value).length > 0;
}, jQuery.validator.format("请输入可见性字符"));

$.validator.addMethod('positive_integer', function (value, element) {
  return !value || /^\+?[1-9][0-9]*$/.test(value);
}, jQuery.validator.format("请输入正整数"));

$.validator.addMethod('unsigned_integer', function (value, element) {
  return this.optional(element) || /^\+?[0-9][0-9]*$/.test(value);
}, jQuery.validator.format("请输入非负整数"));

// jQuery.validator.addMethod("unsigned_integer", function (value, element) {
//   return this.optional(element) || /^([1-9]\d*|0)$/.test(value);
// }, "时长必须为非负整数");

jQuery.validator.addMethod("second_range", function (value, element) {
  return this.optional(element) || /^([0-9]|[012345][0-9]|59)$/.test(value);
}, "请输入0-59之间的数字");

$.validator.addMethod("open_live_course_title", function (value, element, params) {
  return !params || /^[^(<|>|'|"|&|‘|’|”|“)]*$/.test(value);
}, Translator.trans('直播公开课标题暂不支持<、>、\"、&、‘、’、”、“字符'));

$.validator.addMethod("currency", function (value, element, params) {
  return this.optional(element) || /^[0-9]{0,8}(\.\d{0,2})?$/.test(value);
}, jQuery.validator.format('请输入有效价格，最多两位小数，整数位不超个8位！'));

$.validator.addMethod("positive_currency", function (value, element, params) {
  return value > 0 && /^[0-9]{0,8}(\.\d{0,2})?$/.test(value);
}, jQuery.validator.format('请输入大于0的有效价格，最多两位小数，整数位不超过8位！'));

jQuery.validator.addMethod("max_year", function (value, element) {
  return this.optional(element) || value < 100000;
}, "有效期最大值不能超过99,999天");

$.validator.addMethod("before_date", function (value, element, params) {
  let date = new Date(value);
  let afterDate = new Date($(params).val());
  return !value || !$(params).val() || afterDate >= date;
},
  Translator.trans('开始日期应早于结束日期')
);

$.validator.addMethod("after_date", function (value, element, params) {
  let date = new Date(value);
  let afterDate = new Date($(params).val());
  return !value || !$(params).val() || afterDate <= date;
},
  Translator.trans('开始日期应早于结束日期')
);

$.validator.addMethod("after_now", function (value, element, params) {
  let afterDate =  new Date(value.replace(/-/g, '/'));
  return !value || afterDate >=new Date();
},
  Translator.trans('开始时间应晚于当前时间')
);

//日期比较，不进行时间比较
$.validator.addMethod("after_now_date", function (value, element, params) {
  let now = new Date();
  let afterDate = new Date(value);
  let str = now.getFullYear() + "/" + (now.getMonth() + 1) + "/" + now.getDate();
  return !value || afterDate >= new Date(str);
},
  Translator.trans('开始日期应晚于当前日期')
);



//检查将废除
$.validator.addMethod("before", function (value, element, params) {
  return value && $(params).val() >= value;
},
  Translator.trans('开始日期应早于结束日期')
);
//检查将废除
$.validator.addMethod("after", function (value, element, params) {
  
  return value && $(params).val() < value;
},
  Translator.trans('结束日期应晚于开始日期')
);
//检查将废除
$.validator.addMethod("feature", function (value, element, params) {
  return value && (new Date(value).getTime()) > Date.now();
},
  Translator.trans('购买截止时间需在当前时间之后')
);