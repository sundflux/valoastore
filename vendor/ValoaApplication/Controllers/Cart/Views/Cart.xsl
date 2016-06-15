<xsl:stylesheet xmlns:xsl="http://www.w3.org/1999/XSL/Transform" version="1.0" xmlns:php="http://php.net/xsl">

	<xsl:template match="index">
	
		<div class="container" id="cart-page">

			<xsl:if test="cartEmpty">
				<div class="row">
					<div class="col-xs-12 text-center">
						<h3><xsl:value-of select="php:function('\Webvaloa\Webvaloa::translate','CART_IS_EMPTY', 'Babypanda')"/></h3>

						<br/>
						<br/>

						<img src="/public/static/images/sadpanda.png" />
					</div>
				</div>
			</xsl:if>

			<xsl:if test="not(cartEmpty)">
				<h1><xsl:value-of select="php:function('\Webvaloa\Webvaloa::translate','SHOPPING_CART', 'Babypanda')"/></h1>

				<form method="post" action="{php:function('\Webvaloa\Webvaloa::translate','CART_LINK', 'Babypanda')}">
					<input type="hidden" name="token" value="{token}" />

					<div class="row">
						<div class="col-xs-12">

							<div class="panel panel-info">
								<div class="panel-body">

									<xsl:for-each select="cart">

										<xsl:for-each select="size">
											<div class="row cart-product-row">
												<div class="col-sm-2">
													<img class="img-responsive" src="{../thumbnail}"/>
												</div>

												<div class="col-sm-6">
													<h4 class="product-name">
														<strong>
															<a href="#">
																<xsl:if test="../url != ''">
																	<xsl:attribute name="href"><xsl:value-of select="../url"/></xsl:attribute>
																</xsl:if>

								                                <xsl:choose>
								                                    <xsl:when test="../../localePrefix = 'en'">
								                                        <xsl:value-of select="article/fieldValues/en_product_description" disable-output-escaping="yes"/>
								                                    </xsl:when>
								                                    <xsl:otherwise>
								                                        <xsl:value-of select="article/fieldValues/product_description" disable-output-escaping="yes"/>
								                                    </xsl:otherwise>
								                                </xsl:choose>

								                                <xsl:choose>
								                                    <xsl:when test="../../localePrefix = 'en'">
								                                        <xsl:value-of select="../article/fieldValues/en_article_title" disable-output-escaping="yes"/>
								                                    </xsl:when>
								                                    <xsl:otherwise>
								                                        <xsl:value-of select="../article/title"/>
								                                    </xsl:otherwise>
								                                </xsl:choose>

															</a>
														</strong>
													</h4>
													<xsl:if test="size !='' and size != '-1'">
														<span class="text-normal"><xsl:value-of select="size"/></span>
													</xsl:if>
												</div>

												<div class="col-sm-4">
													<div class="col-xs-6 text-right">
														<h5><strong><xsl:value-of select="../price/priceView"/>&#160;€ &#160;&#160;&#160; <span class="text-muted">x</span></strong></h5>
													</div>
													<div class="col-xs-4">
														<input class="form-control show-tooltip cart-amount-change" value="{amount}" type="number" name="amount-{../id}-{hash}" data-toggle="tooltip" data-placement="top" min="1">
															<xsl:if test="max_amount">
																<xsl:attribute name="max">
																	<xsl:value-of select="max_amount" />
																</xsl:attribute>
															</xsl:if>
															<xsl:attribute name="title">
																<xsl:value-of select="php:function('\Webvaloa\Webvaloa::translate','CHANGE_AMOUNT', 'Babypanda')"/>
															</xsl:attribute>
														</input>
													</div>
													<div class="col-xs-2">
														<a href="{php:function('\Webvaloa\Webvaloa::translate','CART_LINK', 'Babypanda')}?remove_product=1&amp;product_id={../id}&amp;token={../../token}&amp;product_hash={hash}" class="btn btn-link btn-remove-from-cart btn-xs show-tooltip" data-toggle="tooltip" data-placement="top"> 
															<xsl:attribute name="title">
																<xsl:value-of select="php:function('\Webvaloa\Webvaloa::translate','REMOVE_FROM_CART', 'Babypanda')"/>
															</xsl:attribute>
															<xsl:attribute name="message">
																<xsl:value-of select="php:function('\Webvaloa\Webvaloa::translate','REMOVE_FROM_CART', 'Babypanda')"/>?
															</xsl:attribute>
															<i class="fa fa-times"></i>
														</a>
													</div>
												</div>

											</div>
										</xsl:for-each>
									</xsl:for-each>

									<br/>

									<div id="update-cart-holder" style="display: none">
										<div class="row">
											<div class="text-center">
												<div class="col-sm-9">
													<xsl:value-of select="php:function('\Webvaloa\Webvaloa::translate','UPDATE_CART_TEXT', 'Babypanda')"/>&#160;&#187;
												</div>
												<div class="col-sm-3 text-left">
													<button type="submit" name="update_cart" class="btn btn-warning btn-lg btn-block" id="update-button">
														<xsl:value-of select="php:function('\Webvaloa\Webvaloa::translate','UPDATE_CART', 'Babypanda')"/>
													</button>
												</div>
											</div>
										</div>
										<hr/>
									</div>

									<div class="row">
										<div class="text-center">
											<div class="col-sm-9">
												<h6 class="text-right"><xsl:value-of select="php:function('\Webvaloa\Webvaloa::translate','PRODUCTS_TOTAL', 'Babypanda')"/></h6>
											</div>
											<div class="col-sm-3 text-left">
												<h5><strong><xsl:value-of select="priceBeforeDiscountView"/>&#160;€</strong></h5>
											</div>
										</div>
									</div>

									<div class="row">
										<div class="text-center">
											<div class="col-sm-9">
												<h6 class="text-right">+ <xsl:value-of select="php:function('\Webvaloa\Webvaloa::translate','DELIVERY', 'Babypanda')"/></h6>
											</div>
											<div class="col-sm-3 text-left">
												<h5><strong><xsl:value-of select="deliveryPrice"/>&#160;€</strong></h5>
											</div>
										</div>
									</div>

									<xsl:if test="discountCode != '' and discountCodeIsValid = '1'">
										<div class="row">
											<div class="text-center">
												<div class="col-sm-9">
													<h6 class="text-right"><xsl:value-of select="php:function('\Webvaloa\Webvaloa::translate','GIFT_CARD', 'Babypanda')"/>&#160;<xsl:value-of select="discountCode"/></h6>
												</div>
												<div class="col-sm-3 text-left">
													<h5><strong>-<xsl:value-of select="discountView"/>&#160;€</strong></h5>
												</div>
											</div>
										</div>
									</xsl:if>

								</div>

						
								<div class="panel-checkout-footer">
									<div class="row text-center">
										<div class="col-md-6">
											<input type="text" class="input input-lg pull-left show-tooltip" name="discount_code" data-toggle="tooltip" data-placement="top">
												<xsl:attribute name="value">
													<xsl:value-of select="discountCode"/>
												</xsl:attribute>
												<xsl:attribute name="placeholder">
													<xsl:value-of select="php:function('\Webvaloa\Webvaloa::translate','ENTER_GIFTCODE', 'Babypanda')"/>
												</xsl:attribute>
												<xsl:attribute name="title">
													<xsl:value-of select="php:function('\Webvaloa\Webvaloa::translate','GIFTCODE_TOOLTIP', 'Babypanda')"/>
												</xsl:attribute>
											</input>
											<button name="verify_giftcode" type="submit" id="gift-card-button">
												<xsl:choose>
													<xsl:when test="discountCodeIsValid = '1'">
														<xsl:attribute name="class">
															btn btn-lg pull-left btn-success
														</xsl:attribute>
														<i class="fa fa-check"></i>
													</xsl:when>
													<xsl:when test="discountCodeIsValid = '0'">
														<xsl:attribute name="class">
															btn btn-lg pull-left btn-danger
														</xsl:attribute>
														<i class="fa fa-refresh"></i>
													</xsl:when>
													<xsl:otherwise>
														<xsl:attribute name="class">
															btn btn-lg pull-left btn-info
														</xsl:attribute>
														<i class="fa fa-refresh"></i>
													</xsl:otherwise>
												</xsl:choose>
											</button>

											<xsl:if test="discountCodeIsValid = '0'">
												<h6>&#171; <xsl:value-of select="php:function('\Webvaloa\Webvaloa::translate','INCORRECT_GIFTCODE', 'Babypanda')"/> </h6>
											</xsl:if>
										</div>
										<div class="col-md-3">
											<h4 class="text-right cart-total-title"><span><xsl:value-of select="php:function('\Webvaloa\Webvaloa::translate','TOTAL', 'Babypanda')"/></span>&#160;<strong><xsl:value-of select="totalView"/>&#160;€</strong>
											<br/>
											<small><xsl:value-of select="php:function('\Webvaloa\Webvaloa::translate','INC_VAT', 'Babypanda')"/></small></h4>
										</div>
										<div class="col-md-3 nopad-right">
											<button type="submit" name="to_checkout" class="btn btn-success btn-lg btn-block" id="checkout-button">
												<xsl:value-of select="php:function('\Webvaloa\Webvaloa::translate','TO_CHECKOUT', 'Babypanda')"/>
											</button>

											<small class="empty-cart">
												<br/>
												<a class="confirm" href="/empty-cart?token={token}">
													<xsl:attribute name="data-message">
														<xsl:value-of select="php:function('\Webvaloa\Webvaloa::translate','CONFIRM_EMPTY_CART', 'Babypanda')"/>
													</xsl:attribute>
													<xsl:value-of select="php:function('\Webvaloa\Webvaloa::translate','EMPTY_CART', 'Babypanda')"/>
												</a>
											</small>
										</div>
									</div>
								</div>
						
							</div>
						</div>
					</div>

				</form>

			</xsl:if>

		</div>
		
	</xsl:template>

</xsl:stylesheet>
