<?xml version="1.0" encoding="utf-8" ?>
<xsl:stylesheet version="1.0" xmlns:xsl="http://www.w3.org/1999/XSL/Transform">

  <!-- Keys first -->
  <xsl:key name="open-closed" match="root/meta" use="status"/>

  <!-- Main template -->
  <xsl:template match="/">
    <html>
      <body>
        <xsl:apply-templates select="root/header"/>
        <xsl:apply-templates select="root/meta"/>
        <table border = "1">
          <tr bgcolor = "#ddd">
            <th>Name</th>
            <th>Login</th>
            <th>Phone</th>
            <th>Email</th>
            <th>Sport</th>
            <th>Year</th>
            <th>Level</th>
          </tr>
          <xsl:apply-templates select="root/visitors"/>
        </table>
      </body>
    </html>
  </xsl:template>

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

  <!-- Heading template -->
  <xsl:template match="root/header">
    <h1><xsl:value-of select="title"/></h1>
    <h2><xsl:value-of select="subheading"/></h2>
    <p><b>
      <xsl:element name="a">
          <xsl:attribute name="href">
              <xsl:value-of select="credit-url"/>
          </xsl:attribute>
          <xsl:attribute name="target">_blank</xsl:attribute>
          Homepage
      </xsl:element>
    </b></p>
    <p><xsl:value-of select="description"/></p>
  </xsl:template>

  <!-- Table template -->
  <xsl:template match="root/visitors/visitor">

      <xsl:for-each select=".">
        <xsl:sort select="name"/> <!-- Comment to remove sorting -->

        <tr>
          <td><xsl:value-of select="name"/></td>
          <td><xsl:value-of select="@login"/></td>
          <td><xsl:value-of select="phone"/></td>
          <td><xsl:value-of select="email"/></td>
          <td><xsl:value-of select="sport/@type"/></td>
          <td>
            <xsl:choose>
              <xsl:when test="year&lt;2000">Antique</xsl:when>
              <xsl:otherwise><xsl:value-of select="year"/></xsl:otherwise>
            </xsl:choose>
          </td>
          <td>
            <xsl:if test="level='admin'">
              <b><xsl:value-of select="level"/></b>
            </xsl:if>
          </td>
        </tr>

      </xsl:for-each>

  </xsl:template>

</xsl:stylesheet>
