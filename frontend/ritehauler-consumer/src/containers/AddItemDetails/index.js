// @flow
import _ from "lodash";
import React, { Component } from "react";
import { ScrollView, View, Keyboard } from "react-native";
import { Actions } from "react-native-router-flux";
import PropTypes from "prop-types";
import { connect } from "react-redux";

import { FloatLabelTextInput } from "../../components";
import { GradientButton } from "../../appComponents";
import { Strings } from "../../theme";
import DataHandler from "../../util/DataHandler";
import ItemVolume from "./ItemVolume";
import styles from "./styles";
import Util from "../../util";

import { addItem, updateItem } from "../../actions/OrderActions";
import { resetItemBox } from "../../actions/ItemBoxActions";

class AddItemDetails extends Component {
  static propTypes = {
    item: PropTypes.object,
    addItem: PropTypes.func.isRequired,
    resetItemBox: PropTypes.func.isRequired,
    updateItem: PropTypes.func.isRequired,
    index: PropTypes.number
  };

  static defaultProps = { item: {}, index: -1 };

  constructor(props) {
    super(props);
    this.onPressButton = this.onPressButton.bind(this);
    this.setFocusWeight = this.setFocusWeight.bind(this);
    this.setFocusLength = this.setFocusLength.bind(this);
    this.setFocusHeight = this.setFocusHeight.bind(this);
    this.onSubmitVolume = this.onSubmitVolume.bind(this);
    this.onChangeTextInputs = this.onChangeTextInputs.bind(this);
  }

  componentWillMount() {
    Keyboard.dismiss();
  }

  shouldComponentUpdate(nextProps) {
    return false;
  }

  componentWillUnmount() {
    this.props.resetItemBox();
  }

  onSubmitVolume() {
    Keyboard.dismiss();
  }

  onPressButton() {
    // get input fields
    const inputFields = [
      this.widthInput,
      this.heightInput,
      this.lengthInput,
      this.weightInput
    ];

    // validate fields
    const isValidFields = Util.validateFields(inputFields);

    // get box
    const itemBox = this.itemVolume.getItemBox();

    // validate box
    const hasBoxId = this.validateBoxInfo(itemBox);

    // isValidate
    const isValid = isValidFields && hasBoxId;

    // set scroll to box if no box
    if (isValidFields && !hasBoxId) {
      this.scrollView.scrollToEnd();
      Util.alert(Strings.errorMessageBox);
    }

    if (isValid) {
      // hide keyboard
      Keyboard.dismiss();

      // set object to add in order
      const itemToAdd = {
        ...this.props.item,
        width: this.widthInput.getValueTextInput(),
        height: this.heightInput.getValueTextInput(),
        length: this.lengthInput.getValueTextInput(),
        weight: this.weightInput.getValueTextInput(),
        volume: itemBox.volume,
        itemBox
      };

      if (this.props.index === -1) {
        // add item in order
        this.props.addItem(itemToAdd);
      } else {
        // update item in order
        this.props.updateItem(itemToAdd, this.props.index);
      }

      // set navigation
      if (DataHandler.isFirstItem()) {
        Actions.createOrder();
      } else {
        Actions.popTo("createOrder");
      }
    }
  }

  onChangeTextInputs = value => {
    const widthInput = this.widthInput.getValueTextInput();
    const heightInput = this.heightInput.getValueTextInput();
    const lengthInput = this.lengthInput.getValueTextInput();
    const hasAllInputs =
      widthInput !== "" && heightInput !== "" && lengthInput !== "";

    if (hasAllInputs) {
      this.itemVolume.sendRequest(widthInput, heightInput, lengthInput);
    } else {
      this.itemVolume.setStateEmpty();
    }
  };

  setFocusWeight() {
    this.weightInput.focus();
  }

  setFocusLength() {
    this.lengthInput.focus();
  }

  setFocusHeight() {
    this.heightInput.focus();
  }

  validateBoxInfo = itemBox => {
    const hasBoxId = !_.isEmpty(itemBox, true) && itemBox.entity_id;

    return hasBoxId;
  };

  _renderItemWidth() {
    const { item } = this.props;
    return (
      <FloatLabelTextInput
        ref={ref => {
          this.widthInput = ref;
        }}
        returnKeyType="next"
        keyboardType="numeric"
        valueText={item.width || ""}
        onSubmitEditing={this.setFocusHeight}
        customContainerStyle={styles.input}
        placeholder={Strings.itemWidthInches}
        onFocusSet={() =>
          Util.scrollToPosition(this.scrollView, this.widthInput)
        }
        onChangeTextInput={this.onChangeTextInputs}
        errorType="decimal"
        errorMessage={Strings.errorMessageWidth}
      />
    );
  }

  _renderItemHeight() {
    const { item } = this.props;
    return (
      <FloatLabelTextInput
        ref={ref => {
          this.heightInput = ref;
        }}
        returnKeyType="next"
        keyboardType="numeric"
        valueText={item.height || ""}
        onSubmitEditing={this.setFocusLength}
        customContainerStyle={styles.input}
        placeholder={Strings.itemHeightInches}
        onFocusSet={() =>
          Util.scrollToPosition(this.scrollView, this.heightInput)
        }
        onChangeTextInput={this.onChangeTextInputs}
        errorType="decimal"
        errorMessage={Strings.errorMessageHeight}
      />
    );
  }

  _renderItemLength() {
    const { item } = this.props;
    return (
      <FloatLabelTextInput
        ref={ref => {
          this.lengthInput = ref;
        }}
        returnKeyType="next"
        keyboardType="numeric"
        valueText={item.length || ""}
        onSubmitEditing={this.setFocusWeight}
        customContainerStyle={styles.input}
        onFocusSet={() =>
          Util.scrollToPosition(this.scrollView, this.lengthInput)
        }
        placeholder={Strings.itemLengthInches}
        onChangeTextInput={this.onChangeTextInputs}
        errorType="decimal"
        errorMessage={Strings.errorMessageLength}
      />
    );
  }

  _renderItemWeight() {
    const { item } = this.props;
    return (
      <FloatLabelTextInput
        ref={ref => {
          this.weightInput = ref;
        }}
        valueText={item.weight || ""}
        keyboardType="numeric"
        onSubmitEditing={this.onSubmitVolume}
        onFocusSet={() =>
          Util.scrollToPosition(this.scrollView, this.weightInput)
        }
        customContainerStyle={styles.input}
        placeholder={Strings.itemWeightUnit}
        errorType="decimal"
        errorMessage={Strings.errorMessageWeight}
      />
    );
  }

  _renderItemVolume() {
    const { item } = this.props;
    return (
      <ItemVolume
        itemData={item.itemBox || {}}
        onItemVolumeChange={this.validateBoxInfo}
        ref={el => {
          if (!!el) {
            this.itemVolume = el.getWrappedInstance();
          }
        }}
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
        keyboardShouldPersistTaps="handled"
        ref={ref => {
          this.scrollView = ref;
        }}
      >
        {this._renderItemWidth()}
        {this._renderItemHeight()}
        {this._renderItemLength()}
        {this._renderItemWeight()}
        {this._renderItemVolume()}
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

const actions = { addItem, updateItem, resetItemBox };

export default connect(
  null,
  actions
)(AddItemDetails);

/*
_renderBoxError() {
    return (
      <BoxError
        ref={ref => {
          this.boxError = ref;
        }}
        errorMessage={Strings.errorMessageCalculateVolume}
      />
    );
  }
{this._renderBoxError()}
*/
/*
    if (hasBoxId) {
      this.boxError.setShowError(false);
    } else {
      this.boxError.setShowError(true);
    }
    */
