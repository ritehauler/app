// @flow
import React, { Component } from "react";
import { ScrollView, View, Keyboard, SafeAreaView } from "react-native";
import KeyboardManager from "react-native-keyboard-manager";
import { Actions } from "react-native-router-flux";
import PropTypes from "prop-types";

import {
  FloatLabelTextInput,
  ButtonView,
  BottomButton
} from "../../components";
import { GradientButton } from "../../appComponents";

import styles from "./styles";
import { Strings, Images } from "../../theme";
import AddImages from "./AddImages";
import ItemReceipt from "./ItemReceipt";
import { EXPENSIVE_ITEM_MINIMUM_PRICE } from "../../constant";
import Utils from "../../util";

class AddItem extends Component {
  static propTypes = {
    item: PropTypes.object,
    index: PropTypes.number
  };

  static defaultProps = { item: {}, index: -1 };

  constructor(props) {
    super(props);

    this.itemName = props.item
      ? { title: props.item.item_name, entity_id: props.item.item_id }
      : undefined;

    this.onPressItemName = this.onPressItemName.bind(this);
    this.onPressButton = this.onPressButton.bind(this);
    this.onChangeTextPrice = this.onChangeTextPrice.bind(this);
    this.onItemNameSelected = this.onItemNameSelected.bind(this);
    this._validate = this._validate.bind(this);
    this._onQuantityChange = this._onQuantityChange.bind(this);
    this._getItemDetails = this._getItemDetails.bind(this);
    if (!Utils.isPlatformAndroid()) {
      KeyboardManager.setEnable(true);
      KeyboardManager.setToolbarPreviousNextButtonEnable(true);
    }
  }

  onPressButton() {
    this._validate();
  }

  _validate() {
    const uploadedImages = this.itemImages.getUploadedImagesArray();

    let receiptImages = [];

    // validating every field for data
    if (this.itemNameInput.getText().length <= 0)
      this.itemNameInput.setError(true);

    if (this.quantityInput.getText().length <= 0)
      this.quantityInput.setError(true);

    if (this.priceInput.getText().length <= 0) this.priceInput.setError(true);

    if (this.itemReceipt.getReceiptDisplay())
      this.itemReceipt.getReceiptImages();

    // check if every field has data if has then move to next screen
    if (
      this.itemNameInput.getText().length &&
      this.quantityInput.getText().length &&
      uploadedImages.length &&
      this.priceInput.getText().length
    ) {
      // if normal validations are true then check if receipt is there if yes then validating it
      if (this.itemReceipt.getReceiptDisplay()) {
        receiptImages = this.itemReceipt.getReceiptImages();
        if (receiptImages.length)
          Actions.addItemDetail({
            item: this._getItemDetails(),
            index: this.props.index
          });
      } else
        Actions.addItemDetail({
          item: this._getItemDetails(),
          index: this.props.index
        });
    }
  }

  _getItemDetails() {
    return {
      ...this.props.item,
      item_name: this.itemName ? this.itemName.title : this.otherItemText,
      item_id: this.itemName ? this.itemName.entity_id : "",
      quantity: this.quantityInput.getText(),
      price: this.priceInput.getText(),
      item_images: this.itemImages.getUploadedImagesArray(),
      is_expensive: this.itemReceipt.getReceiptDisplay() ? 1 : 0,
      item_receipt: this.itemReceipt.getReceiptDisplay()
        ? this.itemReceipt.getReceiptImages()
        : ""
    };
  }

  onPressItemName() {
    Actions.searchItemName({
      cbOnItemNameSelected: this.onItemNameSelected,
      itemName: this.itemName,
      otherItemText: this.otherItemText
    });
  }

  onItemNameSelected(itemName, otherItemText) {
    this.itemName = itemName;
    this.otherItemText = otherItemText;
    if (itemName) {
      this.itemNameInput.setText(itemName.title);
      this.itemNameInput.setError(false);
    } else {
      this.itemNameInput.setText(otherItemText);
    }
  }

  onChangeTextPrice = value => {
    if (value >= EXPENSIVE_ITEM_MINIMUM_PRICE) {
      this.itemReceipt.setReceiptDisplay(true);
    } else {
      this.itemReceipt.setReceiptDisplay(false);
    }
  };

  otherItemText = "";
  itemName = undefined;

  _renderItemName() {
    const { item } = this.props;
    return (
      <ButtonView onPress={this.onPressItemName}>
        <FloatLabelTextInput
          ref={ref => {
            this.itemNameInput = ref;
          }}
          placeholder={Strings.itemName}
          editable={false}
          pointerEvents="none"
          errorMessage="Item name required"
          valueText={item.item_name || ""}
          rightImage={Images.arrowDown}
          customContainerStyle={styles.input}
        />
      </ButtonView>
    );
  }

  _onQuantityChange() {
    this.quantityInput.setError(false);
  }

  _renderQuantity() {
    const { item } = this.props;
    return (
      <FloatLabelTextInput
        ref={ref => {
          this.quantityInput = ref;
        }}
        keyboardType="numeric"
        errorMessage="Item quantity required"
        valueText={item.quantity || ""}
        customContainerStyle={styles.input}
        onChangeTextInput={this._onQuantityChange}
        placeholder={Strings.quantity}
      />
    );
  }

  _renderItemImages() {
    const { item } = this.props;
    return (
      <AddImages
        title={Strings.itemImages}
        ref={ref => {
          this.itemImages = ref;
        }}
        imagesArray={item ? item.item_images : []}
      />
    );
  }

  _renderItemPrice() {
    const { item } = this.props;
    return (
      <FloatLabelTextInput
        ref={ref => {
          this.priceInput = ref;
        }}
        keyboardType="numeric"
        errorMessage="Item price required"
        valueText={item.price || ""}
        customContainerStyle={styles.input}
        placeholder={Strings.actualItemPrice}
        onChangeTextInput={this.onChangeTextPrice}
      />
    );
  }

  _renderItemReceipt() {
    const { item } = this.props;

    const price = item.price || 0;
    const showReceipt = Number(price) >= EXPENSIVE_ITEM_MINIMUM_PRICE;
    return (
      <ItemReceipt
        showReceipt={showReceipt}
        ref={ref => {
          this.itemReceipt = ref;
        }}
        imagesArray={item.item_receipt || []}
      />
    );
  }

  _renderButton() {
    return (
      <GradientButton
        text="Continue"
        ref={ref => {
          this.gradientButton = ref;
        }}
        onPress={this.onPressButton}
      />
    );
  }

  _renderScrollView() {
    return (
      <ScrollView
        style={styles.scrollView}
        contentContainerStyle={styles.contentContainerStyle}
      >
        {this._renderItemName()}
        {this._renderQuantity()}
        {this._renderItemImages()}
        {this._renderItemPrice()}
        {this._renderItemReceipt()}
      </ScrollView>
    );
  }

  render() {
    return (
      <SafeAreaView style={styles.container}>
        <View style={styles.container}>
          {this._renderScrollView()}
          {this._renderButton()}
        </View>
      </SafeAreaView>
    );
  }
}

export default AddItem;
