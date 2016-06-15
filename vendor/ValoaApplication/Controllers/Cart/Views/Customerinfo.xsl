<xsl:stylesheet xmlns:xsl="http://www.w3.org/1999/XSL/Transform" version="1.0" xmlns:php="http://php.net/xsl">

	<xsl:template match="index">
	
		<div class="container" id="customer-info">

			<div class="container">
				<div class="row bs-wizard" style="border-bottom:0;">
                
					<div class="col-xs-4 bs-wizard-step active">
						<div class="text-center bs-wizard-stepnum">Toimitustiedot</div>
						<div class="progress progress-cart-inactive">
							<div class="progress-bar"></div>
						</div>
						<a href="#" class="bs-wizard-dot"></a>
						<div class="bs-wizard-info text-center">Mihin toimitamme?</div>
					</div>
                
					<div class="col-xs-4 bs-wizard-step disabled"><!-- complete -->
						<div class="text-center bs-wizard-stepnum">Maksu</div>
						<div class="progress progress-cart-inactive">
							<div class="progress-bar"></div>
						</div>
						<a href="#" class="bs-wizard-dot"></a>
						<div class="bs-wizard-info text-center">Maksa verkkopankissa tai laskulla.</div>
					</div>
                
					<div class="col-xs-4 bs-wizard-step disabled"><!-- complete -->
						<div class="text-center bs-wizard-stepnum">Valmis</div>
						<div class="progress progress-cart-inactive">
							<div class="progress-bar"></div>
						</div>
						<a href="#" class="bs-wizard-dot"></a>
						<div class="bs-wizard-info text-center">Tilaus valmis! :)</div>
					</div>

				</div>
           </div>

           <br/>

           <div id="cartcontent" />

			<div class="container">

				<div class="row">
					<div class="col-md-9 col-md-offset-1">
						<br/>
						<br/>
						<form class="form-horizontal" role="form" name="order-form" id="order-form">

							<fieldset>
								<div class="form-group">
									<label class="col-sm-2 control-label" for="textinput"></label>
									<div class="col-sm-10">
										<h4>Tilaajan tiedot</h4>
									</div>
								</div>

								<div class="form-group">
									<label class="col-sm-2 control-label" for="textinput">Nimesi</label>
									<div class="col-sm-10">
										<input name="name" type="text" placeholder="Etunimi Sukunimi" class="form-control autosave" required="required" value="{form/name}" />
									</div>
								</div>

          						<br/>

								<div class="form-group">
									<label class="col-sm-2 control-label" for="textinput">Katuosoite</label>
									<div class="col-sm-10">
										<input name="streetaddress" type="text" placeholder="Katuosoite ja talonumero" class="form-control autosave" required="required" value="{form/streetaddress}" />
									</div>
								</div>

								<div class="form-group">
									<label class="col-sm-2 control-label" for="textinput">Postinumero</label>
									<div class="col-sm-4">
										<input name="zip" type="text" placeholder="00000" class="form-control autosave" required="required" id="zip" value="{form/zip}" />
									</div>

									<label class="col-sm-2 control-label" for="textinput">Postitoimipaikka</label>
									<div class="col-sm-4">
										<input name="city" type="text" placeholder="Postitoimipaikka" class="form-control autosave" required="required" value="{form/city}" />
									</div>
								</div>

								<br/>

								<div class="form-group">
									<label class="col-sm-2 control-label" for="textinput">Puhelinnumero</label>
									<div class="col-sm-10">
										<input name="phone" type="text" placeholder="040 1234567" class="form-control autosave" value="{form/phone}" />
									</div>
								</div>

								<div class="form-group">
									<label class="col-sm-2 control-label" for="textinput">Sähköposti</label>
									<div class="col-sm-10">
										<input name="email" type="email" placeholder="Sähköpostiosoite" class="form-control autosave" required="required" value="{form/email}" />
									</div>
								</div>

								<hr />
							</fieldset>

							<fieldset>
								<div class="form-group">
									<label class="col-sm-2 control-label" for="textinput"></label>
									<div class="col-sm-10">
	
									<div class="checkbox checkbox-circle checkbox-info">
										<input type="checkbox" id="delivery-checkbox" style="styled">
											<xsl:choose>
												<xsl:when test="customDeliveryAddress = '1'">
													<xsl:attribute name="checked">checked</xsl:attribute>
												</xsl:when>
											</xsl:choose>
										</input>
										<label for="delivery-checkbox">
											<span>Toimitus eri osoitteeseen / lahjaksi</span>
											<br/>
											<small class="text-muted"> &#160; Huom! Tilattaessa toimitus eri osoitteeseen, <b>laskulla maksaminen ei ole mahdollista.</b> <br/> &#160; Mikäli haluat maksaa Klarna- laskulla tai tilillä, tilaus voidaan toimittaa vain tilaajan osoitteeseen. </small>
										</label>
									</div>

									</div>
								</div>

								<div id="delivery-address">
									<xsl:choose>
										<xsl:when test="customDeliveryAddress = '1'">

										</xsl:when>
										<xsl:otherwise>
											<xsl:attribute name="style">display: none;</xsl:attribute>
										</xsl:otherwise>
									</xsl:choose>

									<div class="form-group">
										<label class="col-sm-2 control-label" for="textinput"></label>
										<div class="col-sm-10">
											<h4>Toimitusosoite</h4>
										</div>
									</div>

									<div class="form-group">
										<label class="col-sm-2 control-label" for="textinput">Vastaanottaja</label>
										<div class="col-sm-10">
											<input type="text" placeholder="Etunimi Sukunimi" class="form-control"  />
										</div>
									</div>

	          						<br/>

									<div class="form-group">
										<label class="col-sm-2 control-label" for="textinput">Katuosoite</label>
										<div class="col-sm-10">
											<input type="text" placeholder="Katuosoite ja talonumero" class="form-control" />
										</div>
									</div>

									<div class="form-group">
										<label class="col-sm-2 control-label" for="textinput">Postinumero</label>
										<div class="col-sm-4">
											<input type="text" placeholder="00000" class="form-control" />
										</div>

										<label class="col-sm-2 control-label" for="textinput">Postitoimipaikka</label>
										<div class="col-sm-4">
											<input type="text" placeholder="Postitoimipaikka" class="form-control" />
										</div>
									</div>

								</div>
							</fieldset>

							<hr />
								<fieldset>
									<div class="form-group">
										<label class="col-sm-2 control-label" for="textinput"></label>
										<div class="col-sm-10">
											<h4>Toimitustapa</h4>
										</div>
									</div>

									<div class="form-group delivery">
										<label class="col-sm-2 control-label" for="textinput"></label>
										<div class="col-sm-10">

											<div class="radio">
				                                <input type="radio" name="delivery_selection" id="delivery_selection1" value="default" class="styled setDeliveryMethod" checked="" />
				                                <label for="delivery_selection1">
				                                    <b>Perustoimitus</b>
				                                </label>
				                            </div>

					                        <h3 class="pull-right"><span class="label label-success"><xsl:value-of select="deliveryPrice"/>&#160;€</span></h3>

				                            <div class="desc">
					                            <p>Toimitus Postin pienlähetyksenä, economy-kirjeenä tai postipakettina. Toimitusaika noin 3-5 arkipäivää.</p>

					                            <button style="display:none" type="button" id="show-other-delivery-methods" class="btn btn-info">Muu toimitustapa <i class="fa fa-angle-down"></i></button>
					                        </div>

										</div>
									</div>

									<div id="other-delivery-methods" style="display: block"> <!-- display: none; -->

										<div class="form-group delivery">
											<label class="col-sm-2 control-label" for="textinput"></label>
											<div class="col-sm-10">

												<div class="radio">
					                                <input type="radio" name="delivery_selection" id="delivery_selection3" value="smartpost" class="styled setDeliveryMethod" />
					                                <label for="delivery_selection3">
					                                    <b>SmartPOST</b>
					                                </label>
					                            </div>

					                            <h3 class="pull-right"><span class="label label-primary">5,90 €</span></h3>

					                            <div class="desc">
						                            <p>Toimitus SmartPOST-lähetyksenä valitsemaasi pakettiautomaattiin.
						                            	SmartPOST-lähetys on noudettavissa pakettiautomaatista pääsääntöisesti lähettämistä seuraavana arkipäivänä klo 16 jälkeen.
						                            </p>

						                            <div id="smartpost-paragraph" style="display: none">
							                            Postinumero: 
							                            <br/>
						                            </div>

						                            <div class="input-group" id="smartpost-selector" style="display:none">
							                            <input type="text" id="smartpost-zip" placeholder="00000" class="form-control" style="float:left" />
							                            <span class="input-group-btn">
															<button type="button" id="updateSmartPost" class="btn btn-default" style="float:left">
																<i class="fa fa-refresh"></i>
																Päivitä
															</button>
														</span>
													</div>

							                        <div id="smartpostContent" style="display: none">
							                        </div>

						                        </div>
											</div>
										</div>

										<div class="form-group delivery">
											<label class="col-sm-2 control-label" for="textinput"></label>
											<div class="col-sm-10">

												<div class="radio">
					                                <input type="radio" name="delivery_selection" id="delivery_selection2" value="package_delivery" class="styled setDeliveryMethod" />
					                                <label for="delivery_selection2">
					                                    <b>Postipaketti</b>
					                                </label>
					                            </div>

					                            <h3 class="pull-right"><span class="label label-primary">6,90 €</span></h3>

					                            <div class="desc">
						                            <p>Toimitus seurantatunnuksellisena postipakettina lähipostiisi. 
						                            	Postipaketti on noudettavissa lähimmästä postista pääsääntöisesti lähettämistä seuraavana arkipäivänä klo 16 jälkeen.
						                            </p>
						                        </div>

											</div>
										</div>

										<div class="form-group delivery">
											<label class="col-sm-2 control-label" for="textinput"></label>
											<div class="col-sm-10">

												<div class="radio">
					                                <input type="radio" name="delivery_selection" id="delivery_selection4" value="fast_delivery" class="styled setDeliveryMethod" />
					                                <label for="delivery_selection4">
					                                    <b>Pikatoimitus</b>
					                                </label>
					                            </div>

					                            <h3 class="pull-right"><span class="label label-primary">6,90 €</span></h3>

					                            <div class="desc">
						                            <p>Tilauksesi käsitellään välittömästi ja lähetetään nopeimmalla mahdollisella toimitustavalla.
						                            	Ennen klo 15:30 saapuneet tilaukset ehtivät saman päivän postiin ja ovat perillä pääsääntöisesti seuraavana arkipäivänä.
						                            </p>
						                        </div>

											</div>
										</div>

									</div>
									
							</fieldset>

							<hr />
							<div id="totals"/>
							<hr />

							<button type="button" name="submit" class="btn btn-lg btn-unvivid btn-i-fix" value="" style="min-width: 200px">
								<xsl:attribute name="onclick">
									window.location.href = '<xsl:value-of select="php:function('\Webvaloa\Webvaloa::translate','SHOPPING_CART_LINK_FULL', 'Babypanda')"/>'
								</xsl:attribute>
								<i class="fa fa-chevron-left"></i> &#160; Palaa ostoskoriin
							</button>
							<button disabled="disabled" type="submit" name="submit" class="btn btn-lg btn-success btn-i-fix pull-right" value="" style="min-width: 200px">Jatka maksuun &#160; <i class="fa fa-chevron-right"></i></button>

					        <br/>
					        <br/>
					        <br/>

						</form>
					</div>
				</div>


			</div>

		</div>

	</xsl:template>

	<xsl:template match="smartpost">
		<br/>

		<xsl:if test="smartpost/errorMessage">
			<xsl:value-of select="smartpost/errorMessage"/>
		</xsl:if>

		<xsl:if test="smartpost/items and smartpost/errorMessage = ''">
			<b>Löytyneet pakettiautomaatit postinumerolle <xsl:value-of select="zip"/></b>
			<br/>

			<xsl:for-each select="smartpost/items">
				<div class="radio">
					<input type="radio" id="smartpost-{FetchLocationSequenceCode}" name="smartpost" value="{FetchLocationSequenceCode}" class="styled">
						<xsl:if test="selected = '1'">
							<xsl:attribute name="checked">
								checked
							</xsl:attribute>
						</xsl:if>
					</input>
					<label for="smartpost-{FetchLocationSequenceCode}">
						<b><xsl:value-of select="PublicName"/></b>
					</label>

					<div class="desc">
						<p>
							<xsl:value-of select="Address"/>
							<br/>
							<xsl:value-of select="PostCode"/>&#160;<xsl:value-of select="City"/>
							<br/>
							<small>
								<xsl:if test="AdditionalInfo != ''">
									<xsl:value-of select="AdditionalInfo"/>
								</xsl:if>
								<br/>
								<xsl:value-of select="Availability"/>
							</small>
						</p>
					</div>
				</div>
			</xsl:for-each>
		</xsl:if>
	</xsl:template>

	<xsl:template match="loadcartcontent">
		<div class="container">
			<div class="row">
				<br/>

				<h4 class="text-center">Ostoskorisi</h4>

				<div class="row">
					<div class="col-sm-2">
					</div>
					<div class="col-sm-5">
					</div>

					<div class="col-sm-3">
						<div class="col-xs-6 text-right">
							Hinta &#160;&#160;&#160;
						</div>
						<div class="col-xs-4 text-left">
							Kpl
						</div>
					</div>
				</div>
				<br/>

				<xsl:for-each select="cart">
					<xsl:for-each select="size">

						<div class="row">
							<div class="col-sm-3"></div>

							<div class="col-sm-4">
								<xsl:value-of select="../article/title"/>
								<xsl:if test="size !='' and size != '-1'">
									&#160;&#160;&#160;
									<span class="text-muted"><xsl:value-of select="size"/></span>
								</xsl:if>
							</div>

							<div class="col-sm-3">
								<div class="col-xs-6 text-right">
									<xsl:value-of select="../price/priceView"/>&#160;€ &#160;&#160;&#160;
								</div>
								<div class="col-xs-4 text-left">
									<xsl:value-of select="amount"/>
								</div>
							</div>

						</div>
					</xsl:for-each>
				</xsl:for-each>


				<div class="row">
					<div class="col-sm-6 col-sm-offset-3">
						<hr/>
					</div>
				</div>

				<div class="row smaller-text">
					<div class="col-sm-3"></div>
					<div class="col-sm-4">
						<xsl:value-of select="php:function('\Webvaloa\Webvaloa::translate','PRODUCTS_TOTAL', 'Babypanda')"/>
					</div>
					<div class="col-sm-3">
						<div class="col-xs-6 text-right">
							<xsl:value-of select="priceBeforeDiscountView"/>&#160;€ &#160;&#160;&#160;
						</div>
						<div class="col-xs-4 text-left">
							
						</div>
					</div>
				</div>

				<div class="row smaller-text">
					<div class="col-sm-3"></div>
					<div class="col-sm-4">
						+ <xsl:value-of select="php:function('\Webvaloa\Webvaloa::translate','DELIVERY', 'Babypanda')"/>
					</div>
					<div class="col-sm-3">
						<div class="col-xs-6 text-right">
							<span id="delivery-price-amount"><xsl:value-of select="deliveryPrice"/></span>&#160;€ &#160;&#160;&#160;
						</div>
						<div class="col-xs-4 text-left">
							
						</div>
					</div>
				</div>

				<xsl:if test="discountCode != '' and discountCodeIsValid = '1'">
					<div class="row smaller-text">
						<div class="col-sm-3"></div>
						<div class="col-sm-4">
							<xsl:value-of select="php:function('\Webvaloa\Webvaloa::translate','GIFT_CARD', 'Babypanda')"/>&#160;<xsl:value-of select="discountCode"/>
						</div>
						<div class="col-sm-3">
							<div class="col-xs-6 text-right">
								-<xsl:value-of select="discountView"/>&#160;€ &#160;&#160;&#160;
							</div>
							<div class="col-xs-4 text-left">
								
							</div>
						</div>
					</div>
				</xsl:if>

				<br/>

				<div class="row smaller-text">
					<div class="col-sm-3"></div>
					<div class="col-sm-4">
						<b><xsl:value-of select="php:function('\Webvaloa\Webvaloa::translate','TOTAL', 'Babypanda')"/>&#160;<xsl:value-of select="php:function('\Webvaloa\Webvaloa::translate','INC_VAT', 'Babypanda')"/></b>
					</div>
					<div class="col-sm-3">
						<div class="col-xs-6 text-right">
							<b><span id="total-price-amount"><xsl:value-of select="totalView"/></span>&#160;€</b> &#160;&#160;&#160;
						</div>
						<div class="col-xs-4 text-left">
							
						</div>
					</div>
				</div>

				<br/>
			</div>
		</div>
	</xsl:template>

	<xsl:template match="loadtotals">
		<table class="table" id="order-totals">
			<tr>
				<td>
					Tuotteet
				</td>
				<td >
					Toimitus
				</td>
				<td style="text-align: right;">
					Yhteensä
				</td>
			</tr>
			<tr>
				<td>
					<h3><xsl:value-of select="priceBeforeDiscountView"/>&#160;€</h3>
				</td>
				<td>
					<h3><xsl:value-of select="deliveryPrice"/>&#160;€</h3>
				</td>
				<td style="text-align: right;">
					<h3><b><xsl:value-of select="totalView"/>&#160;€</b></h3>
				</td>
			</tr>
		</table>
	</xsl:template>

</xsl:stylesheet>
