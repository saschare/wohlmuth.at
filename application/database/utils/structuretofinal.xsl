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
                            <xsl:for-each
                                select="//constraint[@table = current()/@table and @column = current()/@name]">
                                <constraint>
                                    <xsl:attribute name="table" select="@reftable"/>
                                    <xsl:attribute name="column" select="@refcolumn"/>
                                    <xsl:attribute name="onupdate" select="@onupdate"/>
                                    <xsl:attribute name="ondelete" select="@ondelete"/>
                                </constraint>
                            </xsl:for-each>
                        </field>
                    </xsl:for-each>
                    <xsl:for-each select="//index[@table = current()/@table]">
                        <xsl:if test="@name != 'PRIMARY'">
                            <index>
                                <xsl:attribute name="name" select="@name"/>
                                <xsl:attribute name="columns" select="@columns"/>
                                <xsl:attribute name="unique" select="@unique"/>
                            </index>
                        </xsl:if>
                    </xsl:for-each>
                </table>
            </xsl:for-each-group>
        </database>
    </xsl:template>
</xsl:stylesheet>
