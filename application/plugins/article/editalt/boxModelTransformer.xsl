<?xml version="1.0" encoding="UTF-8"?>
<xsl:stylesheet xmlns:xsl="http://www.w3.org/1999/XSL/Transform" version="1.0">
    <xsl:output method="html" encoding="UTF-8" omit-xml-declaration="yes"/>
    <xsl:template match="/">
        <xsl:apply-templates/>
    </xsl:template>
    <xsl:template match="node">
        <div class="boxmodel-root">
            <xsl:apply-templates select="./shortcode"/>
        </div>
    </xsl:template>
    <xsl:template match="shortcode">
        <div class="shortcode">
            <input type="hidden" name="method">
                <xsl:attribute name="value">
                    <xsl:value-of select="./@method"/>
                </xsl:attribute>
            </input>
            <input type="hidden" name="index">
                <xsl:attribute name="value">
                    <xsl:value-of select="./@index"/>
                </xsl:attribute>
            </input>
            <div class="shortcode-title">
                <xsl:value-of select="./@method"/>:<xsl:value-of select="./@index"/>
            </div>
            <xsl:apply-templates select="./shortcode|./code"/>
        </div>
    </xsl:template>
    <xsl:template match="code">
        <xsl:if test="(. != '') and (./@class = 'aitsu_params')">
            <code class="params" style="display:none;">
                <xsl:value-of select="."/>
            </code>
        </xsl:if>
    </xsl:template>
</xsl:stylesheet>
