<%@ page language="java" import="java.util.*" pageEncoding="UTF-8"%>
<%@ taglib uri="/struts-tags" prefix="s"%>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN">

    <%@ include file="../util/calendar.jsp" %>
    <%@ include file="../include/dialog.jsp"%>
<html>
	<script language="JavaScript" src="../js/util.js"></script>
	<script language="JavaScript" type="text/JavaScript">

	function addCourse(f) {
		goFormWindow(f,"addWebsiteAction");
	}
	</script>

  <head>
  	<LINK href="../css/head.css" type=text/css rel=stylesheet>
    <title>添加商户网址</title>

  </head>
  <body>
    <center>
    	<h3>添加商户网址</h3>
    	<div>
    		<s:form action="addMerchantWeb" theme="simple">
    		<table width=615 align=center border=0>
				<tbody>
					<tr>
  						<td>商户号：</td>
  						<td><input type="text" size="25" name="newmerno" value="<s:property value="newmerno"/>"/>
  					</tr>
 					<tr>
						<td>交易网址：</td>
						<td><input type="text" size="70" name="webchannels.tradeWebsite"  value="<s:property value="webchannels.tradeWebsite"/>"/></td>
					</tr>
  					<tr>
  						<td>返回网址：</td>
	    				<td><input type="text" size="70" name="webchannels.website" value="<s:property value="webchannels.website"/>"/></td>
  					</tr>
  					<tr>
  						<td>备注：</td>
	    				<td><input type="text" size="35" name="webchannels.remark" value="<s:property value="webchannels.remark"/>"/></td>
  					</tr>
  					<tr>
	    				<td colspan="2"><font color="red"><s:property value="messageAction"/></font></td>
  					</tr>
  				</tbody>
			</table>
				<input type="submit" value="添加网址" />
			</s:form>
    	</div>
    </center>
  </body>
  <script language="JavaScript" src="../js/util.js"></script>
</html>
