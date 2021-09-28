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
          <xsl:apply-templates select="root/visitor"/>
        </table>
      </body>
    </html>
  </xsl:template>

</xsl:stylesheet>
