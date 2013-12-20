YAHOO.namespace('ARISoft.ariQuiz');
YAHOO.ARISoft.ariQuiz.questions = {
    baseQuestion: function(a, b, c, d) {
        this.questionId = a;
        this.questionData = b;
        this.options = c;
        this.extraParams = d
    }
};
YAHOO.ARISoft.ariQuiz.questions.baseQuestion.prototype = {
    draw: function(a) {},
    validate: function(a) {
        return true
    }
};
YAHOO.ARISoft.ariQuiz.ajaxQuestionManager = function(a) {
    a = a || {};
    a = YAHOO.lang.merge({
        baseUrl: 'index.php',
        skipQuestionTask: 'ajax.skipQuestion',
        getQuestionTask: 'ajax.getQuestion',
        saveQuestionTask: 'ajax.saveQuestion',
        getExplanationTask: 'ajax.getExplanationQuestion',
        stopQuizTask: 'ajax.stopQuiz',
        resumeQuizTask: 'ajax.resumeQuiz',
        getCorrectAnswerTask: 'ajax.getCorrectAnswer',
        option: 'com_ariquiz',
        ticketId: null,
        parsePluginTag: true
    },
    a);
    YAHOO.lang.augmentObject(this, a, true)
};
YAHOO.ARISoft.ariQuiz.ajaxQuestionManager.prototype = {
    questionId: null,
    getQuestion: function(a) {
        var b = this.toQueryString({
            'option': this.option,
            'ticketId': this.ticketId,
            'task': this.getQuestionTask,
            'parseTag': (this.parsePluginTag ? '1': '0')
        });
        YAHOO.util.Connect.asyncRequest('POST', this.baseUrl, a, b)
    },
    getExplanation: function(a, b) {
        var c = {
            'option': this.option,
            'ticketId': this.ticketId,
            'task': this.getExplanationTask,
            'qid': this.questionId
        };
        this.updateForm(a, c);
        c = this.toQueryString(c);
        YAHOO.util.Connect.setForm(a);
        YAHOO.util.Connect.asyncRequest('POST', this.baseUrl, b, c)
    },
    saveQuestion: function(a, b) {
        var c = {
            'option': this.option,
            'ticketId': this.ticketId,
            'task': this.saveQuestionTask,
            'qid': this.questionId
        };
        this.updateForm(a, c);
        c = this.toQueryString(c);
        YAHOO.util.Connect.setForm(a);
        YAHOO.util.Connect.asyncRequest('POST', this.baseUrl, b, c)
    },
    skipQuestion: function(a, b) {
        var c = {
            'option': this.option,
            'ticketId': this.ticketId,
            'task': this.skipQuestionTask,
            'qid': this.questionId
        };
        this.updateForm(a, c);
        c = this.toQueryString(c);
        YAHOO.util.Connect.setForm(a);
        YAHOO.util.Connect.asyncRequest('POST', this.baseUrl, b, c)
    },
    stopQuiz: function(a) {
        var b = this.toQueryString({
            'option': this.option,
            'ticketId': this.ticketId,
            'task': this.stopQuizTask
        });
        YAHOO.util.Connect.asyncRequest('POST', this.baseUrl, a, b)
    },
    resumeQuiz: function(a) {
        var b = this.toQueryString({
            'option': this.option,
            'ticketId': this.ticketId,
            'task': this.resumeQuizTask
        });
        YAHOO.util.Connect.asyncRequest('POST', this.baseUrl, a, b)
    },
    getExplanation: function(a) {
        var b = this.toQueryString({
            'option': this.option,
            'ticketId': this.ticketId,
            'task': this.getExplanationTask,
            'qid': this.questionId
        });
        YAHOO.util.Connect.asyncRequest('POST', this.baseUrl, a, b)
    },
    getCorrectAnswer: function(a, b) {
        var c = {
            'option': this.option,
            'ticketId': this.ticketId,
            'task': this.getCorrectAnswerTask,
            'qid': this.questionId,
            'parseTag': (this.parsePluginTag ? '1': '0')
        };
        this.updateForm(a, c);
        c = this.toQueryString(c);
        YAHOO.util.Connect.setForm(a);
        YAHOO.util.Connect.asyncRequest('POST', this.baseUrl, b, c)
    },
    updateForm: function(a, b) {
        if (!a || !a.elements || !b) return;
        for (var c in b) {
            if (YAHOO.lang.isUndefined(a.elements[c]) || YAHOO.lang.isUndefined(a.elements[c].value)) continue;
            a.elements[c].value = b[c]
        }
    },
    toQueryString: function(a) {
        var b = '';
        if (!a) return b;
        var c = [];
        for (var d in a) {
            var e = a[d];
            c.push(d + '=' + encodeURIComponent(e))
        }
        b = c.join('&');
        return b
    }
};
YAHOO.ARISoft.ariQuiz.questionManager = function(a, b, c) {
    this.ajaxManager = new YAHOO.ARISoft.ariQuiz.ajaxQuestionManager(b);
    a = a || {};
    a = YAHOO.lang.merge({
        mainContainerId: null,
        containerId: null,
        explanationId: null,
        correctAnswerId: null,
        errorContainerId: null,
        formId: null,
        completedCount: 0,
        quizProgressWrapCtrlId: 'ariQuizProgressWrap',
        quizProgressCtrlId: 'ariQuizProgress',
        quizTime: null,
        timeOverCtrlId: 'timeOver',
        questionCount: 0,
        extraParams: null
    },
    a);
    YAHOO.lang.augmentObject(this, a, true);
    this.queOptions = c;
    this.timeParts = [86400, 3600, 60, 1];
    this.timePartsMap = {
        1 : YAHOO.ARISoft.languageManager.getMessage('Date.SecondShort'),
        60 : YAHOO.ARISoft.languageManager.getMessage('Date.MinuteShort'),
        3600 : YAHOO.ARISoft.languageManager.getMessage('Date.HourShort'),
        86400 : YAHOO.ARISoft.languageManager.getMessage('Date.DayShort')
    }
};
YAHOO.ARISoft.ariQuiz.questionManager.prototype = {
    loadingCount: 0,
    currentQuestion: null,
    showError: function(a) {
        YAHOO.util.Dom.get(this.errorContainerId).innerHTML = a;
        YAHOO.util.Dom.addClass(this.mainContainerId, 'ariQuizShowError')
    },
    hideError: function() {
        YAHOO.util.Dom.removeClass(this.mainContainerId, 'ariQuizShowError')
    },
    raiseServerEvent: function(a) {
        if (!this.formId) return false;
        var b = YAHOO.util.Dom.get(this.formId);
        if (!b || YAHOO.lang.isUndefined(b.elements['task'])) return false;
        b.elements['task'].value = 'question$' + a;
        this._showLoading();
        b.submit();
        return true
    },
    initAutoStop: function(a) {
        a = a || this.autoStopTimeout;
        if (a < 1) return;
        this.recreateAutoStopHandler();
        YAHOO.util.Event.addListener(document, 'click', this.recreateAutoStopHandler, this, true);
        YAHOO.util.Event.addListener(document, 'mousemove', this.recreateAutoStopHandler, this, true);
        YAHOO.util.Event.addListener(document, 'keydown', this.recreateAutoStopHandler, this, true)
    },
    destroyAutoStop: function() {
        if (this._autoStopTimeHandler) {
            this._autoStopTimeHandler.cancel();
            this._autoStopTimeHandler = null
        }
        YAHOO.util.Event.removeListener(document, 'click', this.recreateAutoStopHandler);
        YAHOO.util.Event.removeListener(document, 'mousemove', this.recreateAutoStopHandler);
        YAHOO.util.Event.removeListener(document, 'keydown', this.recreateAutoStopHandler)
    },
    recreateAutoStopHandler: function() {
        if (this._autoStopTimeHandler) {
            this._autoStopTimeHandler.cancel();
            this._autoStopTimeHandler = null
        }
        if (this.autoStopTimeout && this.autoStopTimeout > 0) {
            this._autoStopTimeHandler = YAHOO.util.Lang.later(this.autoStopTimeout, this,
            function() {
                this.hideInactivePopup();
                ariQuizQueManager.raiseServerEvent('autoStopExit')
            },
            null, false)
        }
    },
    hideInactivePopup: function() {
        this.destroyAutoStop();
        this.inactivePopup.hide()
    },
    initInactivePopup: function(a) {
        a = a || this.inactiveTimeout;
        if (a < 1) return;
        this.recreateInactiveHandler();
        YAHOO.util.Event.addListener(document, 'click', this.recreateInactiveHandler, this, true);
        YAHOO.util.Event.addListener(document, 'mousemove', this.recreateInactiveHandler, this, true);
        YAHOO.util.Event.addListener(document, 'keydown', this.recreateInactiveHandler, this, true)
    },
    destroyInactivePopup: function() {
        if (this._inactivePopupTimeHandler) {
            this._inactivePopupTimeHandler.cancel();
            this._inactivePopupTimeHandler = null
        }
        YAHOO.util.Event.removeListener(document, 'click', this.recreateInactiveHandler);
        YAHOO.util.Event.removeListener(document, 'mousemove', this.recreateInactiveHandler);
        YAHOO.util.Event.removeListener(document, 'keydown', this.recreateInactiveHandler)
    },
    showInactivePopup: function() {
        this.destroyInactivePopup();
        this.inactivePopup.show();
        this.initAutoStop()
    },
    recreateInactiveHandler: function() {
        if (this._inactivePopupTimeHandler) {
            this._inactivePopupTimeHandler.cancel();
            this._inactivePopupTimeHandler = null
        }
        if (this.inactiveTimeout && this.inactiveTimeout > 0) {
            this._inactivePopupTimeHandler = YAHOO.util.Lang.later(this.inactiveTimeout, this, this.showInactivePopup, null, false)
        }
    },
    resumeQuiz: function() {
        this.ajaxManager.resumeQuiz({
            start: this._showLoading(),
            success: function(a) {
                var b = a.responseText;
                var c = YAHOO.lang.JSON.parse(b);
                if (c) {
                    this.showCurrentQuestion()
                } else {
                    this._reload();
                    return
                }
                this._hideLoading()
            },
            failure: this._onAjaxFailure,
            scope: this
        })
    },
    stopQuiz: function(d) {
        d = d || this.showCurrentQuestion;
        this.ajaxManager.stopQuiz({
            start: this._showLoading(),
            success: function(a) {
                var b = a.responseText;
                var c = YAHOO.lang.JSON.parse(b);
                if (c) {
                    if (a.argument.callback) {
                        a.argument.callback.call(this)
                    }
                } else {
                    this._reload();
                    return
                }
                this._hideLoading()
            },
            failure: this._onAjaxFailure,
            scope: this,
            argument: {
                callback: d
            }
        })
    },
    skipQuestion: function() {
        this.ajaxManager.skipQuestion(YAHOO.ARISoft.DOM.$(this.formId), {
            start: this._showLoading(),
            success: function(a) {
                var b = a.responseText;
                var c = YAHOO.lang.JSON.parse(b);
                if (c) {
                    this.showCurrentQuestion()
                } else {
                    this._reload();
                    return
                }
                this._hideLoading()
            },
            failure: this._onAjaxFailure,
            scope: this
        })
    },
    saveQuestion: function() {
        this.ajaxManager.saveQuestion(YAHOO.ARISoft.DOM.$(this.formId), {
            start: this._showLoading(),
            success: function(a) {
                var b = a.responseText;
                var c = YAHOO.lang.JSON.parse(b);
                if (c && c['result']) {
                    if (c['moveToNext'])++this.completedCount;
                    if (c['showExplanation']) this.showExplanationQuestion();
                    else this.showCurrentQuestion()
                } else {
                    this._reload();
                    return
                }
                this._hideLoading()
            },
            failure: this._onAjaxFailure,
            scope: this
        })
    },
    showCorrectAnswer: function() {
        this.ajaxManager.getCorrectAnswer(YAHOO.ARISoft.DOM.$(this.formId), {
            start: this._showLoading(),
            success: function(a) {
                var b = a.responseText;
                var c = YAHOO.lang.JSON.parse(b);
                if (!c || !YAHOO.util.Dom.get(this.correctAnswerId)) {
                    this._hideLoading();
                    this.hideCorrectAnswer();
                    return
                }
                var d = YAHOO.ARISoft.Quiz.statistics.getStatistics(c);
                if (c.Note) {
                    d += '<br/><b>' + YAHOO.ARISoft.languageManager.getMessage('Label.QuestionNote') + ': </b>' + c.Note
                }
                YAHOO.util.Dom.get(this.correctAnswerId).innerHTML = d;
                YAHOO.util.Dom.addClass(this.mainContainerId, 'ariQuizCorrectAnswer');
                this._hideLoading()
            },
            failure: this._onAjaxFailure,
            scope: this
        })
    },
    hideCorrectAnswer: function() {
        YAHOO.util.Dom.removeClass(this.mainContainerId, 'ariQuizCorrectAnswer')
    },
    showCurrentQuestion: function() {
        this.ajaxManager.getQuestion({
            start: this._showLoading(),
            success: function(a) {
                var b = a.responseText;
                var c = YAHOO.lang.JSON.parse(b);
                if (c) {
                    var d = false;
                    d = (typeof(YAHOO.ARISoft.ariQuiz.questions[c.questionType]) != "undefined");
                    if (d) {
                        c.jQuestion = YAHOO.ARISoft.ariQuiz.questions[c.questionType];
                        var e = (typeof(this.queOptions[c.questionType]) != 'undefined') ? this.queOptions[c.questionType] : null;
                        c.jQuestion = new c.jQuestion(c.questionId, c.questionData, e, this.extraParams);
                        this.ajaxManager.questionId = c.questionId
                    } else {
                        c = null
                    }
                }
                this.currentQuestion = c;
                if (!this._drawQuestion(c)) return;
                this._hideLoading()
            },
            failure: this._onAjaxFailure,
            scope: this
        })
    },
    showExplanationQuestion: function() {
        this.ajaxManager.getExplanation({
            start: this._showLoading(),
            success: function(a) {
                var b = a.responseText;
                var c = YAHOO.lang.JSON.parse(b);
                if (!c || !YAHOO.util.Dom.get(this.explanationId)) {
                    this._hideLoading();
                    this.showCurrentQuestion();
                    return
                }
                var d = YAHOO.ARISoft.Quiz.statistics.getStatistics(c);
                if (c.Note) {
                    d += '<br/><b>' + YAHOO.ARISoft.languageManager.getMessage('Label.QuestionNote') + ': </b>' + c.Note
                }
                YAHOO.util.Dom.get(this.explanationId).innerHTML = d;
                YAHOO.util.Dom.addClass(this.mainContainerId, 'ariQuizExplanation');
                this._hideLoading()
            },
            failure: this._onAjaxFailure,
            scope: this
        })
    },
    hideExplanationQuestion: function() {
        YAHOO.util.Dom.removeClass(this.mainContainerId, 'ariQuizExplanation');
        this.showCurrentQuestion()
    },
    validate: function() {
        var a = true;
        if (this.currentQuestion) a = this.currentQuestion.jQuestion.validate();
        return a
    },
    _onAjaxFailure: function() {
        alert(YAHOO.ARISoft.languageManager.getMessage('Ajax.ServerError'));
        this._hideLoading()
    },
    _showLoading: function() {
        if (this.mainContainerId) {
            YAHOO.util.Dom.addClass(this.mainContainerId, 'ariQuizLoading');
            if (this.loadingCount < 1) this.setEnabledButtons(false); ++this.loadingCount
        }
    },
    _hideLoading: function() {
        if (this.mainContainerId) {--this.loadingCount;
            if (this.loadingCount > 0) return;
            this.loadingCount = 0;
            YAHOO.util.Dom.removeClass(this.mainContainerId, 'ariQuizLoading');
            this.setEnabledButtons(true)
        }
    },
    _reload: function() {
        if (!this.raiseServerEvent('reload')) {
            window.location.href = window.location.href
        }
    },
    _getProgressPercent: function() {
        var a = this.questionCount;
        var b = 0;
        if (a) b = (this.completedCount < a) ? Math.floor((100 * this.completedCount) / a) : 100;
        return b
    },
    _updateQuizProgress: function() {
        var a = YAHOO.ARISoft.DOM.$(this.quizProgressWrapCtrlId);
        var b = YAHOO.ARISoft.DOM.$(this.quizProgressCtrlId);
        var c = YAHOO.ARISoft.DOM.$(this.questionInfoId);
        if (b) b.style.width = this._getProgressPercent() + '%';
        if (a) a.title = YAHOO.ARISoft.core.format('#{cCount} / #{qCount}', {
            cCount: this.completedCount,
            qCount: this.questionCount
        });
        if (c) c.innerHTML = YAHOO.ARISoft.core.format(YAHOO.ARISoft.languageManager.getMessage('Label.JSQuestionInfo'), {
            qIndex: this.currentQuestion ? (parseInt(this.currentQuestion.questionIndex, 10) + 1) : 0,
            qCount: this.questionCount
        })
    },
    _startTimer: function() {
        this.questionTime = this.currentQuestion ? this.currentQuestion.questionTime: null;
        this.startQueTime = (new Date()).getTime();
        if (this.questionTime != null || this.quizTime != null) {
            this._clearTimer();
            this._updateTimer();
            this._updateTimerHandler = YAHOO.util.Lang.later(999, this, this._updateTimer, null, true)
        }
    },
    _clearTimer: function() {
        var a = YAHOO.ARISoft.DOM.$(this.timeContainerId);
        a.innerHTML = '';
        if (this._updateTimerHandler) {
            this._updateTimerHandler.cancel();
            this._updateTimerHandler = null
        }
    },
    _updateTimer: function() {
        var a = this;
        var b = '';
        var c = YAHOO.ARISoft.DOM.$(a.timeContainerId);
        var d = (new Date()).getTime();
        var e = Math.round((d - a.startQueTime) / 1000);
        if (a.questionTime != null) a.questionTime -= e;
        if (a.quizTime != null) a.quizTime -= e;
        a.startQueTime = d;
        if (a.questionTime != null) {
            var f = a.questionTime > 0 ? a.questionTime: 0;
            var g = a._formatQuizTime(f);
            b = (c && a.questionTime < 31) ? '<span class="ariQuizTimeEnd">' + g + '</span>': g
        }
        if (a.quizTime != null) {
            if (b) b += ' / ';
            var f = a.quizTime > 0 ? a.quizTime: 0;
            var g = a._formatQuizTime(f);
            b += (c && a.quizTime < 31) ? '<span class="ariQuizTimeEnd">' + g + '</span>': g
        }
        if (b) b = YAHOO.ARISoft.languageManager.getMessage('Label.RemainingTime') + ' : ' + b;
        if (c) c.innerHTML = b;
        if ((a.questionTime != null && a.questionTime <= 1) || (a.quizTime != null && a.quizTime <= 1)) {
            a.questionTime = 0;
            if (a._updateTimerHandler) a._updateTimerHandler.cancel();
            var h = YAHOO.ARISoft.DOM.$(a.timeOverCtrlId);
            if (h) h.value = 'true';
            a.saveQuestion();
            return
        }
    },
    _formatQuizTime: function(a) {
        var b = '';
        for (var i = 0,
        c = this.timeParts.length; i < c; i++) {
            var d = this.timeParts[i];
            var t = Math.floor(a / d);
            if (t > 0) {
                a -= t * d
            }
            if (t > 0 || b) {
                b += t + ' ' + this.timePartsMap[d] + ' '
            }
        }
        return b
    },
    _drawQuestion: function(a) {
        if (!a) {
            this._reload();
            return false
        }
        YAHOO.ARISoft.DOM.updateHtml(this.queContainerId, a.questionText);
        a.jQuestion.draw(this.containerId);
        this._startTimer();
        this._updateQuizProgress();
        return true
    },
    setEnabledButtons: function(a) {
        var b = document.forms[this.formId];
        var c = YAHOO.ARISoft.DOM.getChildElementsByAttribute(b, 'disabledAfterSubmit');
        if (c) {
            for (var i = 0; i < c.length; i++) {
                var d = c[i];
                if (typeof(d.disabled) != 'undefined') {
                    d.disabled = !a;
                    if (a) YAHOO.util.Dom.removeClass(d, 'ariQuizDisabled');
                    else YAHOO.util.Dom.addClass(d, 'ariQuizDisabled')
                }
            }
        }
    }
};
YAHOO.ARISoft.ariQuiz.questions.SingleQuestion = aris.core.createDerivedClass(YAHOO.ARISoft.ariQuiz.questions.baseQuestion, {
    ViewTypes: {
        'Radio': 0,
        'DropDown': 1
    },
    view: null,
    answersOrderType: 'Numeric',
    constructor: function(a, b, c, d) {
        this.view = this.ViewTypes.Radio;
        var e = null;
        if (b) {
            if (b['data']) e = b['data'];
            if (b['view']) this.view = parseInt(b['view'], 10)
        }
        if (d && d['AnswersOrderType']) this.answersOrderType = d['AnswersOrderType'];
        this.constructor(a, e, c, d)
    },
    getId: function() {
        return 'selectedAnswer_' + this.questionId
    },
    getTableId: function() {
        return 'tblQueContainer_' + this.questionId
    },
    draw: function(a) {
        var b = YAHOO.ARISoft.DOM.$(a);
        var c = '';
        switch (this.view) {
        case this.ViewTypes.DropDown:
            c = this.getDropDownViewHtml();
            break;
        default:
            c = this.getRadioViewHtml();
            break
        }
        b.innerHTML = c
    },
    getRadioViewHtml: function() {
        var a = this.extraParams && this.extraParams['QuestionColsCount'] ? parseInt(this.extraParams['QuestionColsCount'], 10) : 1;
        if (isNaN(a) || a < 1) {
            a = 1
        };
        var b = this.getId();
        var c = '<table id="' + this.getTableId() + '" class="ariQuizAnswersContainer">';
        var d = '<td>			<table border="0" cellpadding="0" cellspacing="0">				<tr>					<td class="ariQuizQuestionLeft ariAnswerChoice" style="white-space: nowrap;"><label for="sa#{hidQueId}">#{choice} #{num}</label>&nbsp;<input type="radio" id="sa#{hidQueId}" name="#{ctrlId}" value="#{hidQueId}" #{checked} /></td>					<td class="ariAnswer">#{tbxAnswer}</td>				</tr>			</table></td>';
        if (this.questionData) {
            var e = this.questionData.length;
            for (var i = 0; i < e; i++) {
                if ((i % a) == 0) {
                    if (i > 0) {
                        c + '</tr>'
                    }
                    c += '<tr>'
                };
                var f = this.questionData[i];
                var g = f['selected'] || false;
                c += YAHOO.ARISoft.core.format(d, {
                    hidQueId: f.hidQueId,
                    ctrlId: b,
                    tbxAnswer: f.tbxAnswer,
                    choice: YAHOO.ARISoft.languageManager.getMessage('Label.Choice'),
                    num: (i + 1),
                    checked: g ? ' checked="chedked"': ''
                })
            };
            if ((e % a) > 0) {
                var h = a - (e % a);
                c += '<td colspan="' + h + '">&nbsp;</td></tr>'
            }
        };
        c += '</table>';
        return c
    },
    getDropDownViewHtml: function() {
        var a = this.getId();
        var b = '<select id="' + a + '" name="' + a + '"><option value="">' + YAHOO.ARISoft.languageManager.getMessage('Label.SelectAnswer') + '</option>';
        if (this.questionData) {
            var c = this.questionData.length;
            var d = '<option value="#{id}"#{selected}>#{answer}</option>';
            for (var i = 0; i < c; i++) {
                var e = this.questionData[i];
                var f = e['selected'] || false;
                b += YAHOO.ARISoft.core.format(d, {
                    id: e.hidQueId,
                    answer: e.tbxAnswer,
                    selected: f ? ' selected="selected"': ''
                })
            }
        }
        b += '</select>';
        return b
    },
    validate: function(a) {
        a = a || false;
        var b = true;
        switch (this.view) {
        case this.ViewTypes.DropDown:
            b = this.validateDropDown();
            break;
        default:
            b = this.validateRadio();
            break
        }
        if (!b && !a) b = confirm(YAHOO.ARISoft.languageManager.getMessage('Validator.QuestionNotSelected'));
        return b
    },
    validateRadio: function() {
        var a = true;
        var b = YAHOO.ARISoft.DOM.getChildElementsByAttribute(this.getTableId(), 'name', this.getId());
        if (b && b.length > 0) {
            a = false;
            for (var i = 0; i < b.length; i++) {
                if (b[i].checked) {
                    a = true;
                    break
                }
            }
        }
        return a
    },
    validateDropDown: function() {
        var a = true;
        var b = document.getElementById(this.getId());
        a = (b && b.value);
        return a
    }
});
YAHOO.ARISoft.ariQuiz.questions.MultipleQuestion = YAHOO.ARISoft.core.createDerivedClass(YAHOO.ARISoft.ariQuiz.questions.baseQuestion, {
    answersOrderType: 'Numeric',
    constructor: function(a, b, c, d) {
        if (d && d['AnswersOrderType']) this.answersOrderType = d['AnswersOrderType'];
        this.constructor(a, b, c, d)
    },
    getId: function() {
        return 'selectedAnswer_' + this.questionId
    },
    getTableId: function() {
        return 'tblQueContainer_' + this.questionId
    },
    draw: function(a) {
        var b = YAHOO.ARISoft.DOM.$(a);
        var c = this.extraParams && this.extraParams['QuestionColsCount'] ? parseInt(this.extraParams['QuestionColsCount'], 10) : 1;
        if (isNaN(c) || c < 1) {
            c = 1
        };
        var d = this.getId();
        var e = '<table cellpadding="0" cellspacing="0" border="0" id="' + this.getTableId() + '" class="ariQuizAnswersContainer">';
        var f = '<td>			<table border="0" cellpadding="0" cellspacing="0">				<tr>					<td class="ariQuizQuestionLeft ariAnswerChoice" style="white-space: nowrap;"><label for="sa#{hidQueId}">#{choice} #{num}</label>&nbsp;<input type="checkbox" id="sa#{hidQueId}" name="#{ctrlId}[]" value="#{hidQueId}"#{checked} /></td>					<td class="ariAnswer">#{tbxAnswer}</td>				</tr>			</table></td>';
        if (this.questionData) {
            var g = this.questionData.length;
            for (var i = 0; i < g; i++) {
                if ((i % c) == 0) {
                    if (i > 0) {
                        e + '</tr>'
                    }
                    e += '<tr>'
                };
                var h = this.questionData[i];
                var j = h['selected'] || false;
                e += YAHOO.ARISoft.core.format(f, {
                    ctrlId: d,
                    hidQueId: h.hidQueId,
                    tbxAnswer: h.tbxAnswer,
                    choice: YAHOO.ARISoft.languageManager.getMessage('Label.Choice'),
                    num: (i + 1),
                    checked: j ? ' checked="checked"': ''
                })
            }
            if ((g % c) > 0) {
                var k = c - (g % c);
                e += '<td colspan="' + k + '">&nbsp;</td></tr>'
            }
        };
        e += '</table>';
        b.innerHTML = e
    },
    validate: function(a) {
        a = a || false;
        var b = true;
        var c = YAHOO.ARISoft.DOM.getChildElementsByAttribute(this.getTableId(), 'name', this.getId());
        if (c && c.length > 0) {
            b = false;
            for (var i = 0; i < c.length; i++) {
                if (c[i].checked) {
                    b = true;
                    break
                }
            }
        }
        if (!b && !a) b = confirm(YAHOO.ARISoft.languageManager.getMessage('Validator.QuestionNotSelected'));
        return b
    }
});
YAHOO.ARISoft.ariQuiz.questions.MultipleSummingQuestion = YAHOO.ARISoft.core.createDerivedClass(YAHOO.ARISoft.ariQuiz.questions.baseQuestion, {
    answersOrderType: 'Numeric',
    constructor: function(a, b, c, d) {
        if (d && d['AnswersOrderType']) this.answersOrderType = d['AnswersOrderType'];
        this.constructor(a, b, c, d)
    },
    getId: function() {
        return 'selectedAnswer_' + this.questionId
    },
    getTableId: function() {
        return 'tblQueContainer_' + this.questionId
    },
    draw: function(a) {
        var b = YAHOO.ARISoft.DOM.$(a);
        var c = this.extraParams && this.extraParams['QuestionColsCount'] ? parseInt(this.extraParams['QuestionColsCount'], 10) : 1;
        if (isNaN(c) || c < 1) {
            c = 1
        };
        var d = this.getId();
        var e = '<table cellpadding="0" cellspacing="0" border="0" id="' + this.getTableId() + '" class="ariQuizAnswersContainer">';
        var f = '<td>			<table border="0" cellpadding="0" cellspacing="0">				<tr>					<td class="ariQuizQuestionLeft ariAnswerChoice" style="white-space: nowrap;"><label for="sa#{hidQueId}">#{choice} #{num}</label>&nbsp;<input type="checkbox" id="sa#{hidQueId}" name="#{ctrlId}[]" value="#{hidQueId}"#{checked} /></td>					<td class="ariAnswer">#{tbxAnswer}</td>				</tr>			</table></td>';
        if (this.questionData) {
            var g = this.questionData.length;
            for (var i = 0; i < g; i++) {
                if ((i % c) == 0) {
                    if (i > 0) {
                        e + '</tr>'
                    }
                    e += '<tr>'
                };
                var h = this.questionData[i];
                var j = h['selected'] || false;
                e += YAHOO.ARISoft.core.format(f, {
                    ctrlId: d,
                    hidQueId: h.hidQueId,
                    tbxAnswer: h.tbxAnswer,
                    choice: YAHOO.ARISoft.languageManager.getMessage('Label.Choice'),
                    num: YAHOO.ARISoft.ariQuiz.questions.util.getAnswerNumber(i + 1, this.answersOrderType),
                    checked: j ? ' checked="checked"': ''
                })
            }
            if ((g % c) > 0) {
                var k = c - (g % c);
                e += '<td colspan="' + k + '">&nbsp;</td></tr>'
            }
        };
        e += '</table>';
        b.innerHTML = e
    },
    validate: function(a) {
        a = a || false;
        var b = true;
        var c = YAHOO.ARISoft.DOM.getChildElementsByAttribute(this.getTableId(), 'name', this.getId());
        if (c && c.length > 0) {
            b = false;
            for (var i = 0; i < c.length; i++) {
                if (c[i].checked) {
                    b = true;
                    break
                }
            }
        }
        if (!b && !a) b = confirm(YAHOO.ARISoft.languageManager.getMessage('Validator.QuestionNotSelected'));
        return b
    }
});
YAHOO.ARISoft.ariQuiz.questions.FreeTextQuestion = YAHOO.ARISoft.core.createDerivedClass(YAHOO.ARISoft.ariQuiz.questions.baseQuestion, {
    getId: function() {
        return 'tbxAnswer_' + this.questionId
    },
    draw: function(a) {
        var b = YAHOO.ARISoft.DOM.$(a);
        var c = this.getId();
        var d = '<input type="text" id="' + c + '" name="' + c + '" class="ariQuizFreeText" autocomplete="off" /><br />';
        b.innerHTML = d;
        if (this.questionData) {
            var e = YAHOO.ARISoft.DOM.$(c);
            e.value = this.questionData['answer']
        }
        new YAHOO.ARISoft.widgets.watermarkText(c, {
            watermarkText: YAHOO.ARISoft.languageManager.getMessage('Label.TextboxWatermarkText'),
            cssClass: 'ariQuizWatermark'
        })
    },
    validate: function(a) {
        a = a || false;
        var b = YAHOO.ARISoft.DOM.$(this.getId());
        var c = b.value;
        c = c.replace(/^\s+|\s+$/g, '');
        var d = !!(c);
        if (!d && !a) d = confirm(YAHOO.ARISoft.languageManager.getMessage('Validator.QuestionEmptyAnswer'));
        return d
    }
});
YAHOO.ARISoft.ariQuiz.questions.HotSpotQuestion = YAHOO.ARISoft.core.createDerivedClass(YAHOO.ARISoft.ariQuiz.questions.baseQuestion, {
    getImgId: function() {
        return 'imgAriHotSpot_' + this.questionId
    },
    getMarkerId: function() {
        return 'imgAriHotSpotMarker' + this.questionId
    },
    getXId: function() {
        return 'hidAriHotSpotX_' + this.questionId
    },
    getYId: function() {
        return 'hidAriHotSpotY_' + this.questionId
    },
    draw: function(a) {
        var b = YAHOO.ARISoft.DOM.$(a);
        var c = this.getXId();
        var d = this.getYId();
        var e = this.getImgId();
        var f = this.getMarkerId();
        var g = (new Date()).getTime();
        var h = this.options.imgLink.replace('__time__', g);
        var i = YAHOO.ARISoft.core.format('<div style="position: relative;" id="divAriHotSpotWrap">				<img id="#{imgId}" style="position: relative; left: 0px; top: 0px;" src="#{imgLink}" />				<img id="#{markerId}" width="9" height="9" style="display: none; position: absolute; z-index: 2;" src="#{markerLink}" />			</div>			<input type="hidden" id="#{hidXId}" name="#{hidXId}" value="-1" />			<input type="hidden" id="#{hidYId}" name="#{hidYId}" value="-1" />', {
            'markerLink': this.options.baseUrl + '/components/com_ariquiz/images/circle.gif',
            'imgLink': h,
            'hidXId': c,
            'hidYId': d,
            'imgId': e,
            'markerId': f
        });
        b.innerHTML = i;
        if (this.questionData) {
            var x = typeof(this.questionData['x']) != 'undefined' ? parseInt(this.questionData['x'], 10) : -1,
            y = typeof(this.questionData['y']) != 'undefined' ? parseInt(this.questionData['y'], 10) : -1;
            if (x > -1 && y > -1) {
                var j = YAHOO.util.Dom.get(f);
                YAHOO.util.Dom.setStyle(j, 'top', (y - 4) + 'px');
                YAHOO.util.Dom.setStyle(j, 'left', (x - 4) + 'px');
                YAHOO.util.Dom.setStyle(j, 'display', 'block');
                YAHOO.util.Dom.get(c).value = x;
                YAHOO.util.Dom.get(d).value = y
            }
        }
        YAHOO.util.Event.on(e, 'mousedown', this._mouseDownHandler, this, true)
    },
    _mouseDownHandler: function(a) {
        var b = YAHOO.ARISoft.DOM.$(this.getMarkerId());
        var c = this._getCursorPosition(a, 'divAriHotSpotWrap');
        b.style.left = (c.x - 4) + 'px';
        b.style.top = (c.y - 4) + 'px';
        YAHOO.util.Dom.setStyle(b, 'display', 'block');
        var d = this.getXId();
        var e = this.getYId();
        YAHOO.ARISoft.DOM.$(d).value = c.x;
        YAHOO.ARISoft.DOM.$(e).value = c.y
    },
    _getCursorPosition: function(e, a) {
        a = YAHOO.ARISoft.DOM.$(a);
        if (typeof(e.offsetX) != 'undefined') return {
            x: e.offsetX,
            y: e.offsetY
        };
        var b = YAHOO.util.Dom.getXY(a);
        if (!YAHOO.env.ua.opera) {
            while (a.tagName != 'BODY') {
                b[1] -= a.scrollTop || 0;
                b[0] -= a.scrollLeft || 0;
                a = a.parentNode
            }
        }
        e = e || event;
        var c = YAHOO.util.Event.getXY(e);
        return {
            x: (c[0] - b[0]),
            y: (c[1] - b[1])
        }
    },
    validate: function(a) {
        a = a || false;
        var b = this.getXId();
        var c = YAHOO.ARISoft.DOM.$(b);
        var d = c && (c.value != '-1');
        if (!d && !a) d = confirm(YAHOO.ARISoft.languageManager.getMessage('Validator.HotspotNotSelected'));
        return d
    }
});
YAHOO.ARISoft.ariQuiz.questions.CorrelationQuestion = YAHOO.ARISoft.core.createDerivedClass(YAHOO.ARISoft.ariQuiz.questions.baseQuestion, {
    getId: function() {
        return 'ddlVariant_' + this.questionId
    },
    getTableId: function() {
        return 'tblQueContainer_' + this.questionId
    },
    draw: function(a) {
        var b = YAHOO.ARISoft.DOM.$(a);
        var c = this.getId();
        var d = '<table id="' + this.getTableId() + '" class="ariQuizCorAnsContainer">';
        var e = '				<tr>				<td class="ariQuizCorLbl">#{tbxAnswer}</td>				<td><select _ariQType="correlation" id="#{ddlId}" name="#{ctrlId}[#{lblId}]">#{options}</select></td>			</tr>';
        if (this.questionData) {
            var f = '<option value="">' + YAHOO.ARISoft.languageManager.getMessage('Label.SelectAnswer') + '</option>';
            var g = this.questionData.answers.length;
            for (var i = 0; i < g; i++) {
                var h = this.questionData.answers[i];
                f += YAHOO.ARISoft.core.format('<option value="#{id}">#{answer}</option>', {
                    id: h.id,
                    answer: h.answer
                })
            }
            g = this.questionData.labels.length;
            for (var i = 0; i < g; i++) {
                var h = this.questionData.labels[i];
                var j = c + h.id.replace(/\./g, '');
                d += YAHOO.ARISoft.core.format(e, {
                    ddlId: j,
                    lblId: h.id,
                    tbxAnswer: h.label,
                    options: f,
                    ctrlId: c
                })
            }
        }
        d += '</table>';
        b.innerHTML = d;
        if (this.questionData) {
            if (this.questionData.correlations) {
                var k = this.questionData.correlations;
                for (var l in k) {
                    var m = k[l];
                    if (!m) continue;
                    var n = YAHOO.util.Dom.get(c + l.replace(/\./g, ''));
                    n.value = m
                }
            }
        }
    },
    validate: function(a) {
        a = a || false;
        var b = true;
        var c = YAHOO.ARISoft.DOM.getChildElementsByAttribute(this.getTableId(), '_ariQType', 'correlation');
        if (c) {
            var d = c.length;
            var e = [];
            for (var i = 0; i < d; i++) {
                var f = c[i];
                var g = f.value;
                if (!g || e.indexOf(g) > -1) {
                    b = !a ? confirm(YAHOO.ARISoft.languageManager.getMessage('Validator.QuestionNotSelected')) : false;
                    break
                }
                e.push(g)
            }
        }
        return b
    }
});
YAHOO.ARISoft.ariQuiz.questions.CorrelationDDQuestion = YAHOO.ARISoft.core.createDerivedClass(YAHOO.ARISoft.ariQuiz.questions.baseQuestion, {
    HANDLER_CLASS: 'ariQuizCorDDEl',
    TARGET_CLASS: 'ariQuizCorTargetEl',
    HANDLER_TARGET_CLASS: 'ariQuizCorDDTEl',
    PH_TARGET_CLASS: 'ariQuizCorPhTargetEl',
    DD_GROUP: 'corQuestion',
    targets: [],
    COR_ID_ATTR: '_corId',
    getId: function() {
        return 'hidCorrelation_' + this.questionId
    },
    getTableId: function() {
        return 'tblQueContainer_' + this.questionId
    },
    draw: function(a) {
        var b = this.getId();
        var c = YAHOO.ARISoft.DOM.$(a);
        var d = '<table id="' + this.getTableId() + '" width="100%">';
        var e = '				<tr>				<td class="ariQuizCorLbl"><div ' + this.COR_ID_ATTR + '="#{labelId}" class="' + this.TARGET_CLASS + ' ariQuizCorDDLabel" style="position: relative;"><div class="' + this.HANDLER_TARGET_CLASS + '" style="position: absolute; height: 100%; width: 10px; right: 0; top: 5px;">&nbsp;</div><div style="padding-right: 10px;">#{label}</div></div></td>				<td><div class="ariQuizCorPhOuter"><div class="' + this.PH_TARGET_CLASS + '"><div ' + this.COR_ID_ATTR + '="#{answerId}" class="' + this.HANDLER_CLASS + ' ariQuizCorDDAnswer"><div class="ariQuizCorDDAnswerMarker"> </div><div>#{answer}</div></div></div></div></td>			</tr>';
        if (this.questionData) {
            var f = this.questionData.answers.length;
            for (var i = 0,
            f = this.questionData.answers.length; i < f; i++) {
                var g = this.questionData.answers[i];
                var h = this.questionData.labels[i];
                d += YAHOO.ARISoft.core.format(e, {
                    answerId: g['id'],
                    answer: g['answer'],
                    labelId: h['id'],
                    label: h['label']
                })
            }
        };
        d += '</table><input type="hidden" id="' + b + '" name="' + b + '" value="" />';
        c.innerHTML = d;
        this._createDragDrop(a, this.questionData)
    },
    _createDragDrop: function(c, d) {
        if (!d) return;
        this.targets = [];
        var e = YAHOO.ARISoft.ariQuiz.questions.util.DDQuizCorrelation;
        var f = this.DD_GROUP;
        var g = this;
        YAHOO.util.Dom.getElementsByClassName(this.PH_TARGET_CLASS, null, c,
        function(a) {
            new YAHOO.util.DDTarget(a, f)
        });
        YAHOO.util.Dom.getElementsByClassName(this.TARGET_CLASS, null, c,
        function(a) {
            g.targets.push((new YAHOO.util.DDTarget(a, f)))
        });
        YAHOO.util.Dom.getElementsByClassName(this.HANDLER_CLASS, null, c,
        function(a) {
            var b = new e(a, f, {});
            b.on('endDragEvent',
            function() {
                this.collectData()
            },
            null, g)
        })
    },
    collectData: function() {
        var a = [];
        var b = this.targets || [];
        for (var i = 0,
        l = b.length; i < l; i++) {
            var c = b[i];
            if (c.player) {
                var d = c.getEl();
                var e = c.player.getEl();
                var f = d.getAttribute(this.COR_ID_ATTR);
                var g = e.getAttribute(this.COR_ID_ATTR);
                a.push({
                    'labelId': f,
                    'answerId': g
                })
            }
        };
        var h = YAHOO.util.Dom.get(this.getId());
        h.value = YAHOO.lang.JSON.stringify(a);
        return a
    },
    validate: function(a) {
        a = a || false;
        var b = true;
        var c = this.collectData();
        var d = this.targets || [];
        if (c.length < d.length) {
            b = !a ? confirm(YAHOO.ARISoft.languageManager.getMessage('Validator.QuestionNotSelected')) : false
        };
        return b
    }
});
YAHOO.namespace('ARISoft.ariQuiz.questions.util');
YAHOO.ARISoft.ariQuiz.questions.util = {
    DDQuizCorrelation: function(a, b, c) {
        YAHOO.ARISoft.ariQuiz.questions.util.DDQuizCorrelation.superclass.constructor.apply(this, arguments);
        this.initCtrl(a, b, c)
    },
    getAnswerNumber: function(a, b) {
        var c;
        switch (b) {
        case 'AlphaUpper':
            c = String.fromCharCode(64 + a);
            break;
        case 'AlphaLower':
            c = String.fromCharCode(96 + a);
            break;
        default:
            c = a;
            break
        }
        return c
    }
};
YAHOO.extend(YAHOO.ARISoft.ariQuiz.questions.util.DDQuizCorrelation, YAHOO.util.DDProxy, {
    TYPE: 'DDQuizCorrelation',
    ACTIVE_PH_CLASS: 'ariQuizCorDDAnswerOnLabel',
    initCtrl: function(a, b, c) {
        if (!a) {
            return
        };
        var d = this.getDragEl();
        YAHOO.util.Dom.setStyle(d, "borderColor", "transparent");
        YAHOO.util.Dom.setStyle(d, "opacity", 0.76);
        this.isTarget = false;
        this.originalStyles = [];
        this.type = YAHOO.ARISoft.ariQuiz.questions.util.DDQuizCorrelation.TYPE;
        this.slot = null;
        this.startPos = YAHOO.util.Dom.getXY(this.getEl())
    },
    startDrag: function(x, y) {
        var a = YAHOO.util.Dom;
        var b = this.getDragEl();
        var c = this.getEl();
        this.isInPlace = YAHOO.util.Dom.hasClass(c, this.ACTIVE_PH_CLASS);
        this.removeActivePhClass(c);
        b.innerHTML = c.innerHTML;
        b.className = c.className;
        a.setStyle(b, "color", a.getStyle(c, "color"));
        a.setStyle(b, "backgroundColor", a.getStyle(c, "backgroundColor"));
        a.setStyle(c, "opacity", 0.1)
    },
    endDrag: function(e) {
        YAHOO.util.Dom.setStyle(this.getEl(), "opacity", 1);
        if (!this.isDragDrop && this.isInPlace) {
            this.addActivePhClass(this.getEl())
        }
        this.isDragDrop = false;
        this.isInPlace = null
    },
    onDragDrop: function(e, a) {
        var b;
        this.isDragDrop = true;
        if ("string" == typeof a) {
            b = YAHOO.util.DDM.getDDById(a)
        } else {
            b = YAHOO.util.DDM.getBestMatch(a)
        };
        var c = this.getEl();
        if (b.player) {
            if (this.slot) {
                if (YAHOO.util.DDM.isLegalTarget(b.player, this.slot)) {
                    YAHOO.util.DDM.moveToEl(b.player.getEl(), c);
                    this.slot.player = b.player;
                    b.player.slot = this.slot
                } else {
                    YAHOO.util.Dom.setXY(b.player.getEl(), b.player.startPos);
                    this.slot.player = null;
                    b.player.slot = null
                }
                if (this.isInPlace) {
                    this.addActivePhClass(b.player.getEl())
                } else {
                    this.removeActivePhClass(b.player.getEl())
                }
            } else {
                b.player.slot = null;
                YAHOO.util.DDM.moveToEl(b.player.getEl(), c)
            }
        } else {
            if (this.slot) {
                this.slot.player = null
            }
        };
        var d = YAHOO.util.Dom.getElementsByClassName('ariQuizCorDDTEl', null, b.getEl());
        if (d && d.length > 0) {
            this.addActivePhClass(c);
            YAHOO.util.DDM.moveToEl(c, d[0])
        } else {
            YAHOO.util.DDM.moveToEl(c, b.getEl())
        };
        this.slot = b;
        this.slot.player = this
    },
    addActivePhClass: function(a) {
        YAHOO.util.Dom.addClass(a, this.ACTIVE_PH_CLASS)
    },
    removeActivePhClass: function(a) {
        YAHOO.util.Dom.removeClass(a, this.ACTIVE_PH_CLASS)
    },
    swap: function(a, b) {
        var c = YAHOO.util.Dom;
        var d = c.getXY(a);
        var e = c.getXY(b);
        c.setXY(a, e);
        c.setXY(b, d)
    },
    onDragOver: function(e, a) {},
    onDrag: function(e, a) {}
});
YAHOO.ARISoft.ariQuiz.questions.MultiFreeTextQuestion = YAHOO.ARISoft.core.createDerivedClass(YAHOO.ARISoft.ariQuiz.questions.baseQuestion, {
    draw: function(a) {
        var b = YAHOO.ARISoft.DOM.$(a);
        b.innerHTML = '';
        if (!this.questionData) return;
        var c = this.qManager.getQuestionText();
        for (var i = 0,
        l = this.questionData.length; i < l; i++) {
            var d = this.questionData[i];
            var e = d['alias'];
            var f = d['id'];
            if (!e || !f) continue;
            c = c.replace('{$' + e + '}', '<input type="text" id="tbxAnswer' + f + '" name="tbxAnswer[' + f + ']" class="ariQuizMultiFreeText" />')
        }
        this.qManager.setQuestionText(c);
        for (var i = 0,
        l = this.questionData.length; i < l; i++) {
            var d = this.questionData[i];
            var f = d['id'];
            var g = YAHOO.util.Dom.get('tbxAnswer' + f);
            if (g) {
                new YAHOO.ARISoft.widgets.watermarkText('tbxAnswer' + f, {
                    watermarkText: YAHOO.ARISoft.languageManager.getMessage('Label.TextboxWatermarkText'),
                    cssClass: 'ariQuizSmallWatermark'
                })
            }
        }
    },
    validate: function() {
        var a = true;
        for (var i = 0,
        l = this.questionData.length; i < l; i++) {
            var b = this.questionData[i];
            var c = b['alias'];
            var d = b['id'];
            var e = YAHOO.ARISoft.DOM.$('tbxAnswer' + d);
            if (!e) continue;
            var f = YAHOO.lang.trim(e.value);
            if (! (f)) {
                a = false;
                break
            }
        }
        if (!a) a = confirm(YAHOO.ARISoft.languageManager.getMessage('Validator.QuestionEmptyAnswer'));
        return a
    }
});