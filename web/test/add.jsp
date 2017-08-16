<%@ page language="java" pageEncoding="UTF-8"%>
<%@ taglib uri="/struts-tags" prefix="s" %>
<%@ taglib prefix="pages" uri="/xs-pages" %>
<html>
  <head>
  	<LINK href="../css/head.css" type=text/css rel=stylesheet>
    <title>添加商户卡种</title>
  </head>
      <script language="JavaScript" type="text/JavaScript">
	function addCourse(f) {
			goFormWindow(f,"../PaySystem/addMerCreditCard");
	}
</script>
  <body>

    <div id="title" value="添加商户卡种"/>
<div id="resizetable" width="400" height="180">
	    <s:form action="addMerChannel" theme="simple">
	    <div style="margin:0 0 0 50px;">
		<table borderColor=#ffffff cellSpacing=0 cellPadding=0 width="240" align=center
		bgColor=#ffffff borderColorLight=#000000 border=1 height="10">
		    	<tr>
		    		<td>商户号</td>
		    		<td>
		    			<input type="hidden" name="merid" value="<s:property value="merchant.id"/>"/>
		    			<input type="hidden" name="merno" value="<s:property value="merchant.merno"/>"/>
		    			<input type="hidden" name="mercreditcardId" value="<s:property value="mercreditcardId"/>"/>
		    			<s:property value="merchant.merno"/>
		    		</td>
		    	</tr>
		    	<tr>
		    		<td>通道</td>
		    		<td>
		    			<s:select name="channelId" list="channelList" listKey="id" listValue="channelName"/>
		    		</td>
		    	</tr>
		    	<tr>
		    		<td>卡种</td>
		    		<td>
		    			<s:select name="mcc.creditCardId" list="creditCardList" listKey="id" listValue="cardName"/>
		    		</td>
		    	</tr>
		    	<tr>
		    		<td>通道排序：</td>
		    		<td>
		    			<input type="text" name="channelSort" value="<s:property value="mcc.channelSort"/>"/>
		    		</td>
		    	</tr>
		    </table>
		    <input type="button" onClick="addCourse(this.form);" value="提交" class="windows_icon1"/>
	    </div>
	    </s:form>


  </body>
</html>
