<xsl:stylesheet version="1.0" xmlns:xsl="http://www.w3.org/1999/XSL/Transform">
	<xsl:variable name="icons_src_dir">.</xsl:variable>
	<xsl:variable name="editmode">false</xsl:variable>

	<xsl:template match="item">
		<div align="center" onclick="event.cancelBuble = true;">
			<img border='0'>
				<xsl:if test="./@type='dir'">
					<xsl:attribute name="src"><xsl:value-of select="$icons_src_dir"/>/icone_dossier.png</xsl:attribute>
					<xsl:attribute name="title"><xsl:value-of select="./@name"/></xsl:attribute>
				</xsl:if>
				<xsl:if test="./@type!='dir'">
					<xsl:attribute name="src"><xsl:value-of select="$icons_src_dir"/>/icone_<xsl:value-of select="./@ext"/>.png</xsl:attribute>
					<xsl:attribute name="title"><xsl:value-of select="./@name"/></xsl:attribute>
				</xsl:if>
				<xsl:attribute name="onerror">this.onerror = null;this.src='<xsl:value-of select="$icons_src_dir"/>/icone_unknown.png';</xsl:attribute>
			</img>
			<div class="dhx_folders_FICON_item_text">
				<!-- read-only mode -->
				<xsl:if test="$editmode='false'">
					<xsl:attribute name="onclick">
						if(this.parentNode.parentNode.className=='dhx_folders_FICON_item_selected'){this.parentNode.parentNode.itemObj.edit(true)}
					</xsl:attribute>
					<xsl:if test="./@type='dir'">
						<xsl:value-of select="./@name"/>
					</xsl:if>
					<xsl:if test="./@type!='dir'">
						<xsl:value-of select="./@name"/>
					</xsl:if>
				</xsl:if>
				<!-- edit mode -->
				<xsl:if test="$editmode='true'">
					<textarea onclick="event.cancelBubble=true;">
						<xsl:attribute name="onblur">
							this.parentNode.parentNode.parentNode.itemObj.edit(false, this.value);
						</xsl:attribute>
						<xsl:value-of select="./@name"/>
					</textarea>
				</xsl:if>
			</div>
		</div>
	</xsl:template>
	
</xsl:stylesheet>