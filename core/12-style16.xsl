<?xml version="1.0" encoding="utf-8" ?>
<xsl:stylesheet version="1.0" xmlns:xsl="http://www.w3.org/1999/XSL/Transform">

  <!-- Import our top meta -->
  <xsl:import href="structure.xsl"/>
  <xsl:import href="meta.xsl"/>
  <xsl:import href="heading.xsl"/>
  <xsl:import href="visitors.xsl"/>
  <xsl:template match="/">
    <xsl:apply-imports/>
  </xsl:template>

</xsl:stylesheet>
