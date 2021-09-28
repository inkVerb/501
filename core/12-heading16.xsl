<?xml version="1.0" encoding="utf-8" ?>
<xsl:stylesheet version="1.0" xmlns:xsl="http://www.w3.org/1999/XSL/Transform">

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

</xsl:stylesheet>
