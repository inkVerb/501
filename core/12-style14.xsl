<?xml version="1.0" encoding="utf-8" ?>
<xsl:stylesheet version="1.0" xmlns:xsl="http://www.w3.org/1999/XSL/Transform">
  <xsl:template match="/">

    <html>
      <body>
        <h1>Visitors</h1>
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

          <xsl:for-each select="root/visitor">
            <xsl:sort select="name"/> <!-- Comment to remove sorting -->

            <tr>
              <td><xsl:value-of select="name"/></td>
              <td><xsl:value-of select="login"/></td>
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
                <xsl:if test="@level='admin'">
                  <b><xsl:value-of select="@level"/></b>
                </xsl:if>
              </td>
            </tr>

          </xsl:for-each>
        </table>
      </body>
    </html>

  </xsl:template>
</xsl:stylesheet>
