// @flow
import React, { Component } from "react";
import { ScrollView, View, Keyboard } from "react-native";
import { Actions } from "react-native-router-flux";
import { connect } from "react-redux";
import PropTypes from "prop-types";

import { FloatLabelTextInput, ButtonView } from "../../components";
import DataHandler from "../../util/DataHandler";
import { GradientButton } from "../../appComponents";
import styles from "./styles";
import { Strings, Images } from "../../theme";
import Utils from "../../util";

import AddImages from "./AddImages";
import ItemReceipt from "./ItemReceipt";

import { request as generalSettingsRequest } from "../../actions/GeneralSettingsActions";

class AddItem extends Component {
  static propTypes = {
    item: PropTypes.object,
    index: PropTypes.number,
    generalSettingsRequest: PropTypes.func.isRequired
  };

  static defaultProps = { item: {}, index: -1 };

  constructor(props) {
    super(props);
    this.onPressItemName = this.onPressItemName.bind(this);
    this.onPressButton = this.onPressButton.bind(this);
    this.onChangeTextPrice = this.onChangeTextPrice.bind(this);
    this.onItemNameSelected = this.onItemNameSelected.bind(this);
    this.setFocusPrice = this.setFocusPrice.bind(this);
    this.onSubmitPrice = this.onSubmitPrice.bind(this);

    this.otherItemText = props.item.otherItemText || "";
    this.itemName = props.item.itemName || undefined;
  }

  componentWillMount() {
    Keyboard.dismiss();
  }

  componentDidMount() {
    if (DataHandler.isFirstItem()) {
      // send request to send general settings
      this.props.generalSettingsRequest();
    }
  }

  onPressItemName() {
    Actions.searchItemName({
      onItemNameSelectionDone: this.onItemNameSelected,
      itemName: this.itemName,
      otherItemText: this.otherItemText
    });
  }

  onItemNameSelected(itemName, otherItemText) {
    // set variables
    this.itemName = itemName;
    this.otherItemText = otherItemText;

    // set text
    if (itemName) {
      this.itemNameInput.setText(itemName.title);
    } else {
      this.itemNameInput.setText(otherItemText);
    }

    // set validation
    this.itemNameInput.checkValidation(false, false, true);
  }

  onSubmitPrice() {
    Keyboard.dismiss();
  }

  onPressButton() {
    // get data
    const itemImages = this.itemImages.getUploadedImagesArray();
    const showReceipt = this.itemReceipt.getShowReceipt();
    const receiptImages = this.itemReceipt.getReceiptImages();
    const inputFields = [
      this.itemNameInput,
      this.quantityInput,
      this.priceInput
    ];

    // validate variables
    const validateInputFields = Utils.validateFields(inputFields);
    const validateItemImages = itemImages.length > 0;
    const validateReceiptImages =
      showReceipt === false || receiptImages.length > 0;
    const isValidate =
      validateInputFields && validateItemImages && validateReceiptImages;
    const isItemNameEmpty = this.itemNameInput.getValueTextInput() === "";
    const allFieldsValidExpectReceiptImages =
      validateInputFields && validateItemImages && !validateReceiptImages;

    // set focus on start and end
    if (isItemNameEmpty) {
      this.scrollView.scrollTo({ x: 0, y: 0, animated: true });
    } else if (allFieldsValidExpectReceiptImages) {
      this.scrollView.scrollToEnd();
    }

    // if validate add item
    if (isValidate) {
      Keyboard.dismiss();
      const item = {
        ...this.props.item,
        itemImages,
        showReceipt,
        receiptImages,
        otherItemText: this.otherItemText,
        itemName: this.itemName,
        price: this.priceInput.getValueTextInput(),
        quantity: this.quantityInput.getValueTextInput()
      };
      Actions.addItemDetail({ item, index: this.props.index });
    }
  }

  onChangeTextPrice = value => {
    const price = value === "" ? 0 : Number(value);
    if (Utils.isItemExpensive(price)) {
      this.itemReceipt.setReceiptDisplay(true);
    } else {
      this.itemReceipt.setReceiptDisplay(false);
    }
  };

  setFocusPrice() {
    this.priceInput.focus();
  }

  otherItemText = "";
  itemName = undefined;

  _renderItemName() {
    const { item } = this.props;
    const itemName =
      item.itemName && item.itemName.title ? item.itemName.title : "";
    return (
      <FloatLabelTextInput
        ref={ref => {
          this.itemNameInput = ref;
        }}
        placeholder={Strings.itemName}
        editable={false}
        pointerEvents="none"
        onPress={this.onPressItemName}
        disableRipple={false}
        valueText={item.otherItemText || itemName}
        rightImage={Images.navigation}
        customContainerStyle={styles.input}
        errorType="required"
        errorMessage={Strings.errorMessageItemName}
      />
    );
  }

  _renderQuantity() {
    const { item } = this.props;
    return (
      <FloatLabelTextInput
        ref={ref => {
          this.quantityInput = ref;
        }}
        returnKeyType="next"
        keyboardType="numeric"
        valueText={item.quantity || ""}
        customContainerStyle={styles.input}
        placeholder={Strings.quantity}
        onSubmitEditing={this.setFocusPrice}
        onFocusSet={() =>
          Utils.scrollToPosition(this.scrollView, this.quantityInput)
        }
        errorType="numeric"
        errorMessage={Strings.errorMessageQuantity}
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
        imagesArray={item.itemImages || []}
      />
    );
  }

  _renderItemPrice() {
    const { item } = this.props;
    const placeholder = Strings.actualItemPrice.replace(
      "==",
      DataHandler.currency()
    );
    return (
      <FloatLabelTextInput
        ref={ref => {
          this.priceInput = ref;
        }}
        keyboardType="numeric"
        valueText={item.price || ""}
        customContainerStyle={styles.input}
        placeholder={placeholder}
        onSubmitEditing={this.onSubmitPrice}
        onChangeTextInput={this.onChangeTextPrice}
        autoCapitalize="none"
        onFocusSet={() =>
          Utils.scrollToPosition(this.scrollView, this.priceInput)
        }
        errorType="decimal"
        errorMessage={Strings.errorMessagePrice}
      />
    );
  }

  _renderItemReceipt() {
    const { item } = this.props;
    return (
      <ItemReceipt
        showReceipt={item.showReceipt || false}
        ref={ref => {
          this.itemReceipt = ref;
        }}
        imagesArray={item.receiptImages || []}
      />
    );
  }

  _renderButton() {
    return <GradientButton onPress={this.onPressButton} />;
  }

  _renderScrollView() {
    return (
      <ScrollView
        style={styles.scrollView}
        contentContainerStyle={styles.contentContainerStyle}
        keyboardShouldPersistTaps="handled"
        ref={ref => {
          this.scrollView = ref;
        }}
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
      <View style={styles.container}>
        {this._renderScrollView()}
        {this._renderButton()}
      </View>
    );
  }
}

const actions = {
  generalSettingsRequest
};

export default connect(
  null,
  actions
)(AddItem);
