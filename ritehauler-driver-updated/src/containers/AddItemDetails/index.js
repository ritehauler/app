// @flow
import _ from "lodash";
import React, { Component } from "react";
import { ScrollView, View, Keyboard, SafeAreaView } from "react-native";
import { Actions } from "react-native-router-flux";
import KeyboardManager from "react-native-keyboard-manager";
import PropTypes from "prop-types";
import { GradientButton } from "../../appComponents";
import { FloatLabelTextInput } from "../../components";
import { connect } from "react-redux";
import styles from "./styles";
import { Strings, Images } from "../../theme";
import DataHandler from "../../util/DataHandler";
import Utils from "../../util";
import ItemVolume from "./ItemVolume";
// redux actions
import { addItem, updateItem } from "../../actions/OrderActions";
import { resetItemBox } from "../../actions/ItemBoxActions";

class AddItemDetails extends Component {
  static propTypes = {
    item: PropTypes.object,
    index: PropTypes.number
  };

  static defaultProps = { item: {}, index: -1 };

  constructor(props) {
    super(props);
    this.onPressButton = this.onPressButton.bind(this);
    this.setFocusWeight = this.setFocusWeight.bind(this);
    this.setFocusLength = this.setFocusLength.bind(this);
    this.setFocusHeight = this.setFocusHeight.bind(this);
    this.onChangeTextInputs = this.onChangeTextInputs.bind(this);
    this._validate = this._validate.bind(this);

    if (!Utils.isPlatformAndroid()) {
      KeyboardManager.setEnable(true);
      KeyboardManager.setToolbarPreviousNextButtonEnable(true);
    }
  }

  state = {
    hasAllInputs: false
  };

  componentWillMount() {
    Keyboard.dismiss();
  }

  componentDidMount() {
    setTimeout(() => {
      this.widthInput.focus();
    }, 500);
  }

  onPressButton() {
    this._validate();
  }

  _validate() {
    if (this.widthInput.getText().length <= 0) this.widthInput.setError(true);
    if (this.heightInput.getText().length <= 0) this.heightInput.setError(true);
    if (this.lengthInput.getText().length <= 0) this.lengthInput.setError(true);
    if (this.weightInput.getText().length <= 0) this.widthInput.setError(true);
    // get box
    const itemBox = this.itemVolume.getItemBox();

    // validate box
    const hasBoxId = this.validateBoxInfo(itemBox)
      ? this.validateBoxInfo(itemBox)
      : this.props.item
        ? this.props.item.item_box_id
        : undefined;

    const boxVolume = itemBox.volume
      ? itemBox.volume
      : this.props.item
        ? this.props.item.volume
        : undefined;

    if (
      this.widthInput.getText().length &&
      this.heightInput.getText().length &&
      this.lengthInput.getText().length &&
      this.weightInput.getText().length &&
      hasBoxId &&
      boxVolume
    ) {
      if (this.props.index === -1) {
        // add item in order
        this.props.addItem(this._getDetails());
      } else {
        // update item in order
        this.props.updateItem(this._getDetails(), this.props.index);
      }

      if (!Utils.isCreateOrderInStack()) {
        Actions.createOrder();
        Utils.createOrderLock(true);
      } else {
        Actions.popTo("createOrder");
      }
    }
  }

  // details taken on this screen
  _getDetails() {
    return {
      ...this.props.item,
      height: this.heightInput.getText(),
      width: this.widthInput.getText(),
      length: this.lengthInput.getText(),
      weight: this.weightInput.getText(),
      volume: this.itemVolume.getItemBox().volume
        ? this.itemVolume.getItemBox().volume
        : this.props.item.volume,
      item_box_id: this.itemVolume.getItemBox().entity_id
        ? this.itemVolume.getItemBox().entity_id
        : this.props.item.item_box_id,
      item_box_title: this.itemVolume.getItemBox().title
        ? this.itemVolume.getItemBox().title
        : this.props.item.item_box_title
    };
  }

  validateBoxInfo = itemBox => {
    const hasBoxId = !_.isEmpty(itemBox, true) && itemBox.entity_id;

    return hasBoxId;
  };

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

  _renderItemWidth() {
    const { item } = this.props;
    return (
      <FloatLabelTextInput
        ref={ref => {
          this.widthInput = ref;
        }}
        blurOnSubmit={false}
        returnKeyType="next"
        keyboardType="numeric"
        valueText={item.width || ""}
        onSubmitEditing={this.setFocusHeight}
        errorMessage="Item width required"
        customContainerStyle={styles.input}
        placeholder={Strings.itemWidthInches}
        onChangeTextInput={this.onChangeTextInputs}
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
        blurOnSubmit={false}
        returnKeyType="next"
        keyboardType="numeric"
        valueText={item.height || ""}
        onSubmitEditing={this.setFocusLength}
        errorMessage="Item height required"
        customContainerStyle={styles.input}
        placeholder={Strings.itemHeightInches}
        onChangeTextInput={this.onChangeTextInputs}
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
        blurOnSubmit={false}
        returnKeyType="next"
        keyboardType="numeric"
        valueText={item.length || ""}
        onSubmitEditing={this.setFocusWeight}
        errorMessage="Item length required"
        customContainerStyle={styles.input}
        placeholder={Strings.itemLengthInches}
        onChangeTextInput={this.onChangeTextInputs}
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
        returnKeyType="done"
        valueText={item.weight || ""}
        keyboardType="numeric"
        customContainerStyle={styles.input}
        placeholder={Strings.itemWeightKg}
      />
    );
  }

  _renderItemVolume() {
    const { item } = this.props;
    const rest = _.isEmpty(item)
      ? {}
      : { volume: 20, cartonImage: Images.carton1 };
    return (
      <ItemVolume
        itemData={item.itemBox || {}}
        onItemVolumeChange={this.validateBoxInfo}
        ref={el => {
          if (el) {
            this.itemVolume = el.getWrappedInstance();
          }
        }}
        {...rest}
      />
    );
  }

  _renderButton() {
    return (
      <GradientButton
        text={this.state.hasAllInputs ? "Done" : "Continue"}
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
      <SafeAreaView style={styles.container}>
        <View style={styles.container}>
          {this._renderScrollView()}
          {this._renderButton()}
        </View>
      </SafeAreaView>
    );
  }
}
const actions = { addItem, updateItem, resetItemBox };

export default connect(null, actions)(AddItemDetails);
