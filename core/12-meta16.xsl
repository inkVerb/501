<?xml version="1.0" encoding="utf-8" ?>
<xsl:stylesheet version="1.0" xmlns:xsl="http://www.w3.org/1999/XSL/Transform">

  <!-- Top meta (Status/Case) -->
  <xsl:template match="root/meta">

    <!-- Use the <xsl:key> -->
    <xsl:choose>
      <!-- If key returns true -->
      <xsl:when test="key('open-closed', 'open')">
        <p>
          Status: <b><xsl:value-of select="status"/></b>
        </p>
      </xsl:when>
      <!-- If key returns false -->
      <xsl:when test="key('open-closed', 'closed')">
        <p>
          Case: <b><xsl:value-of select="status"/></b>
        </p>
      </xsl:when>

      <!-- Message -->
      <xsl:otherwise>
        <!-- Change to terminate="yes" to kill script when <meta><status> is empty-->
        <xsl:message terminate="no">Status open-closed not set!</xsl:message>
      </xsl:otherwise>

    </xsl:choose>

  </xsl:template>

</xsl:stylesheet>
