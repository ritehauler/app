// @flow
import _ from "lodash";
import React, { Component } from "react";
import { View, Keyboard, ScrollView } from "react-native";
import { Actions } from "react-native-router-flux";
import { connect } from "react-redux";
import PropTypes from "prop-types";

import {
  GradientButton,
  SearchBar,
  TextInputContainer
} from "../../appComponents";
import { ServerRequestPage } from "../../components";
import IQKeyboardManager from "../../config/IQKeyboardManager";
import { Strings } from "../../theme";
import ListNames from "./ListNames";
import Utils from "../../util";
import styles from "./styles";

import { ENTITY_TYPE_ID_ITEM } from "../../config/WebService";
import { request as itemNamesRequest } from "../../actions/ItemNameActions";

class SearchItemName extends Component {
  static propTypes = {
    onItemNameSelectionDone: PropTypes.func.isRequired,
    itemName: PropTypes.object,
    otherItemText: PropTypes.string,
    itemNames: PropTypes.object.isRequired,
    networkInfo: PropTypes.object.isRequired,
    itemNamesRequest: PropTypes.func.isRequired
  };

  static defaultProps = { itemName: undefined, otherItemText: "" };

  constructor(props) {
    super(props);

    this.onPressButton = this.onPressButton.bind(this);
    this.onChangeOtherItemText = this.onChangeOtherItemText.bind(this);
    this.onPressItem = this.onPressItem.bind(this);
    this.onSearchTextChange = this.onSearchTextChange.bind(this);
    this.fetchData = this.fetchData.bind(this);
    this.renderContent = this.renderContent.bind(this);

    this.otherItemText = props.otherItemText;
    this.itemName = props.itemName;
  }

  componentWillMount() {
    Keyboard.dismiss();
    
    setTimeout(() => {
      IQKeyboardManager.setToolbarPreviousNextButtonEnable(false);
    }, 500);
    //IQKeyboardManager.setToolbarPreviousNextButtonEnable(false);
    
  }

  componentDidMount() {
    this.fetchData();
  }

  componentWillUnmount() {
    setTimeout(() => {
      IQKeyboardManager.setToolbarPreviousNextButtonEnable(true);
    }, 500);
    //IQKeyboardManager.setToolbarPreviousNextButtonEnable(true);
  }

  onPressButton() {
    if (this.itemName || this.otherItemText !== "") {
      this.props.onItemNameSelectionDone(this.itemName, this.otherItemText);
      Actions.pop();
    } else {
      Utils.alert(Strings.errorMessageItemName);
    }
  }

  onChangeOtherItemText = text => {
    this.listNames.setSelectedId(0);
    this.otherItemText = text;
    this.itemName = undefined;
  };

  onPressItem = item => {
    this.otherItem.setText("");
    this.otherItemText = "";
    this.itemName = item;
  };

  onSearchTextChange = searchText => {
    this.listNames.setSearchResults(searchText);
    if (searchText === "") {
      this.otherItem.setHideBox(false);
    } else {
      this.otherItem.setHideBox(true);
    }
  };

  fetchData() {
    const payload = {
      entity_type_id: ENTITY_TYPE_ID_ITEM,
      is_other: 0,
      status: 1,
      mobile_json: 1
    };
    this.props.itemNamesRequest(payload);
  }

  otherItemText = "";
  itemName = undefined;

  _renderFlatList() {
    const { data } = this.props.itemNames;
    return (
      <ListNames
        onPressItem={this.onPressItem}
        ref={ref => {
          this.listNames = ref;
        }}
        selectedId={this.itemName ? this.itemName.entity_id : 0}
        data={data}
      />
    );
  }

  _renderSearchBar() {
    return (
      <SearchBar
        placeHolder={Strings.searchItems}
        ref={ref => {
          this.searchBar = ref;
        }}
        onSearchTextChange={this.onSearchTextChange}
      />
    );
  }

  _renderOtherItem() {
    return (
      <TextInputContainer
        onChange={this.onChangeOtherItemText}
        ref={ref => {
          this.otherItem = ref;
        }}
        text={this.otherItemText}
        title={Strings.otherItem}
        placeHolder={Strings.enterName}
      />
    );
  }

  _renderButton() {
    return (
      <GradientButton
        ref={ref => {
          this.gradientButton = ref;
        }}
        onPress={this.onPressButton}
      />
    );
  }

  _renderScrollView() {
    return (
      <ScrollView>
        {this._renderFlatList()}
        {this._renderOtherItem()}
      </ScrollView>
    );
  }

  renderContent() {
    return (
      <View style={styles.container}>
        {this._renderSearchBar()}
        {this._renderScrollView()}
        {this._renderButton()}
      </View>
    );
  }

  render() {
    const { networkInfo, itemNames } = this.props;
    const { data, isFetching, errorMessage, failure } = itemNames;
    const isInternetConnected = networkInfo.isNetworkConnected;
    return (
      <ServerRequestPage
        data={data}
        errorMessage={errorMessage}
        failure={failure}
        renderView={this.renderContent}
        isFetching={isFetching}
        isInternetConnected={isInternetConnected}
        fetchData={this.fetchData}
        emptyMessage={Strings.noItemNamesFound}
      />
    );
  }
}

const mapStateToProps = store => ({
  itemNames: store.itemNames,
  networkInfo: store.networkInfo
});
const actions = { itemNamesRequest };

export default connect(
  mapStateToProps,
  actions
)(SearchItemName);
