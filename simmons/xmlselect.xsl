<?xml version='1.0'?>
<xsl:stylesheet xmlns:xsl="http://www.w3.org/1999/XSL/Transform" version="1.0">

<xsl:output method='html'/>

<!-- param values may be changed during the XSL Transformation -->
<xsl:param name="title" type="string">Simmons Picks</xsl:param>
<xsl:param name="year" type="integer"></xsl:param>
<xsl:param name="week" type="integer"></xsl:param>
<xsl:param name="pick" type="string"></xsl:param>
<xsl:param name="home" type="string"></xsl:param>
<xsl:param name="opp" type="string"></xsl:param>
<xsl:param name="lineopr" type="string"></xsl:param>
<xsl:param name="line" type="integer"></xsl:param>
<xsl:param name="result" type="string"></xsl:param>

<xsl:template match="/">

<html>
  <head>
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
    <title><xsl:value-of select="$title"/></title>
    <style type="text/css">
      <![CDATA[
      <!--
        caption { font-weight: bold; }
        tr.odd { background: #eeeeee; }
        tr.even { background: #dddddd; }
        .center { margin: 0px auto;}
      -->
      ]]>
    </style>

</head>
<body>
  
  <div class="center">

  <table class="center" border="0">
    <caption><xsl:value-of select="$title"/></caption>
    <thead>
      <tr>
        <th>Year</th>
        <th>Week</th>
        <th>Pick</th>
        <th>Home</th>
	<th>Opponent</th>
        <th>Line</th>
        <th>Result</th>
      </tr>
    </thead>
	  
    <tbody>
      <xsl:apply-templates select="//picks" />
    </tbody>
	  
  </table>
  </div>
</body>
</html>

</xsl:template>

<xsl:template match="picks">

  <tr>
    <xsl:attribute name="class">
      <xsl:choose>
        <xsl:when test="position()mod 2">odd</xsl:when>
        <xsl:otherwise>even</xsl:otherwise>
      </xsl:choose>
    </xsl:attribute>

    <td><xsl:value-of select="year"/></td>
    <td><xsl:value-of select="week"/></td>
    <td><xsl:value-of select="pick"/></td>
    <td><xsl:value-of select="home"/></td>
    <td><xsl:value-of select="opp"/></td>
    <td><xsl:value-of select="line"/></td>
    <td><xsl:value-of select="result"/></td>
  </tr>

</xsl:template>

</xsl:stylesheet>
