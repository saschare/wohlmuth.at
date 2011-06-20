<?xml version="1.0" encoding="UTF-8"?>
<xsl:stylesheet xmlns:xsl="http://www.w3.org/1999/XSL/Transform" version="2.0">
    <xsl:template match="root">
        <database>
            <xsl:for-each-group select="field" group-by="@table">
                <table>
                    <xsl:attribute name="name" select="@table"/>
                    <xsl:attribute name="engine" select="@engine"/>
                    <xsl:for-each select="current-group()">
                        <field>
                            <xsl:attribute name="name" select="@name"/>
                            <xsl:attribute name="type" select="@type"/>
                            <xsl:attribute name="default" select="@default"/>
                            <xsl:if test="nullable = 'yes'">
                                <xsl:attribute name="nullable">true</xsl:attribute>
                            </xsl:if>
                            <xsl:if test="@extra = 'auto_increment'">
                                <xsl:attribute name="autoincrement">true</xsl:attribute>
                            </xsl:if>
                            <xsl:if test="@key = 'pri'">
                                <xsl:attribute name="primary">true</xsl:attribute>
                            </xsl:if>
                        </field>
                    </xsl:for-each>
                    <xsl:for-each-group select="index" group-by="@table">
                        <xsl:for-each select="current-group()">
                            <index>
                                <xsl:attribute name="name" select="@name"/>
                            </index>
                        </xsl:for-each>
                    </xsl:for-each-group>
                </table>
            </xsl:for-each-group>
        </database>
    </xsl:template>
</xsl:stylesheet>
