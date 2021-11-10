<?xml version="1.0" encoding="utf-8"?>
<xsl:stylesheet version="3.0"
                xmlns:xsl="http://www.w3.org/1999/XSL/Transform"
                xmlns:atom="http://www.w3.org/2005/Atom"
                xmlns:dc="http://purl.org/dc/elements/1.1/"
                xmlns:itunes="http://www.itunes.com/dtds/podcast-1.0.dtd">

  <xsl:output method="html" version="1.0" encoding="UTF-8" indent="yes"/>
  <xsl:template match="/">

    <html xmlns="http://www.w3.org/1999/xhtml">

      <head>
        <title><xsl:value-of select="/rss/channel/title"/> RSS Feed</title>
        <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
        <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1"/>

        <style type="text/css">

          body {
            font-family: sans-serif;
            background-color: #eee;
          }

          div.head {
            padding: 1px 20px 10px 20px;
            margin: 5px 10px 10px 10px;
            background-color: #fff;
          }

          div.head img {
            float: right;
            margin: 10px 0px 10px 10px;
          }

          div.item {
            background-color: #fff;
            margin: 5px 10px 10px 10px;
            padding: 1px 20px 10px 20px;
          }

          div.item div.date {
            font-size: 10pt;
            color: #444;
          }

          div.item div.description {
            font-size: 11pt;
          }

        </style>

      </head>

      <body>

		<!-- Head of rendered page -->
        <div class="head">

          <!-- RSS image -->
          <xsl:if test="/rss/channel/image">
            <a class="head-logo">
              <xsl:attribute name="href">
                <xsl:value-of select="/rss/channel/link"/>
              </xsl:attribute>
              <img>
                <xsl:attribute name="src">
                  <xsl:value-of select="/rss/channel/image/url"/>
                </xsl:attribute>
                <xsl:attribute name="title">
                  <xsl:value-of select="/rss/channel/title"/>
                </xsl:attribute>
              </img>
            </a>
          </xsl:if>

          <!-- RSS title & description -->
            <h1><xsl:value-of select="/rss/channel/title"/></h1>
            <a class="top" target="_blank">
              <xsl:attribute name="href">
                <xsl:value-of select="/rss/channel/link"/>
              </xsl:attribute>
              <p><xsl:value-of select="/rss/channel/description"/><xsl:text disable-output-escaping="yes">&#160;&#8594;</xsl:text></p>
            </a>

          <!-- Applies if iTunes podcast image present -->
          <xsl:if test="/rss/channel/itunes:image">
            <br/><p>Podcast feed</p>
          </xsl:if>

        </div>

        <!-- Applies if Atom feed elements are present -->
        <xsl:if test="/rss/channel/atom:link[@rel='alternate']">
          <div>

            <xsl:for-each select="/rss/channel/atom:link[@rel='alternate']">
              <a target="_blank">
                <xsl:attribute name="class">
                  <xsl:value-of select="@icon"/>
                </xsl:attribute>
                <xsl:attribute name="href">
                  <xsl:value-of select="@href"/>
                </xsl:attribute>
                <xsl:value-of select="@title"/>
              </a>
            </xsl:for-each>

          </div>
        </xsl:if>

        <!-- Iterate each feed item -->
        <xsl:for-each select="/rss/channel/item">
          <div class="item">

            <!-- Title with link -->
            <h2 class="item-title">
              <a target="_blank">
                <xsl:attribute name="href">
                  <xsl:value-of select="link"/>
                </xsl:attribute>
                <xsl:value-of select="title"/>
              </a>
            </h2>

            <!-- Date -->
            <div class="date">
              <span><i><xsl:value-of select="pubDate" /></i></span>
              <xsl:if test="itunes:duration">
                <xsl:text disable-output-escaping="yes"> &#x2022; </xsl:text>
                <span><xsl:value-of select="itunes:duration" /></span>
              </xsl:if>
            </div>

            <!-- Applies only if iTunes elements are present (RSS doesn't have subtitle, only iTunes) -->
            <xsl:if test="itunes:subtitle">
              <h3><xsl:value-of select="itunes:subtitle" /></h3>
            </xsl:if>

            <!-- Duration is also an iTunes-only RSS element, not normal in many other podcast feeds -->
            <xsl:if test="itunes:duration">
              <audio controls="true" preload="none">
                <xsl:attribute name="src">
                  <xsl:value-of select="enclosure/@url"/>
                </xsl:attribute>
              </audio>
            </xsl:if>

            <!-- Description -->
            <div class="description">
              <span><xsl:value-of disable-output-escaping="yes" select="description" /></span>
              <hr />
            </div>

          </div>
        </xsl:for-each>

      </body>

    </html>

  </xsl:template>
</xsl:stylesheet>
