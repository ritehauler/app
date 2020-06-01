// @flow
import React, { Component } from "react";
import { Keyboard, View, ScrollView } from "react-native";
import HTMLView from "react-native-htmlview";
import { connect } from "react-redux";
import PropTypes from "prop-types";

import { Separator, ServerRequestPage } from "../../components";
import { Strings } from "../../theme";
import webStyle from "./webStyle";
import styles from "./styles";

import { ENTITY_TYPE_ID_CMS } from "../../config/WebService";
import {
  request as cmsContentRequest,
  resetCmsContent
} from "../../actions/CmsContentActions";

class Content extends Component {
  static propTypes = {
    cmsContentRequest: PropTypes.func.isRequired,
    resetCmsContent: PropTypes.func.isRequired,
    cmsContent: PropTypes.object.isRequired,
    networkInfo: PropTypes.object.isRequired,
    slug: PropTypes.string.isRequired,
    isLogin: PropTypes.bool
  };

  static defaultProps = { isLogin: false };

  constructor(props) {
    super(props);
    this.fetchData = this.fetchData.bind(this);
    this.renderContent = this.renderContent.bind(this);
  }

  componentWillMount() {
    Keyboard.dismiss();
  }

  componentDidMount() {
    this.fetchData();
  }

  componentWillUnmount() {
    this.props.resetCmsContent();
  }

  fetchData() {
    const payload = {
      entity_type_id: ENTITY_TYPE_ID_CMS,
      slug: this.props.slug,
      mobile_json: 1
    };
    this.props.cmsContentRequest(payload);
  }

  renderContent() {
    const { cmsContent } = this.props;
    const htmlContent = cmsContent.data.desc.replace(/(\r\n|\n|\r)/gm, "");
    return (
      <ScrollView
        style={styles.scroll}
        contentContainerStyle={styles.contentContainerStyle}
      >
        <HTMLView
          value={htmlContent}
          stylesheet={webStyle}
          addLineBreaks={false}
        />
      </ScrollView>
    );
  }

  render() {
    const { networkInfo, cmsContent, isLogin } = this.props;
    const { data, isFetching, errorMessage, failure } = cmsContent;
    const isInternetConnected = networkInfo.isNetworkConnected;
    return (
      <View style={isLogin ? styles.containerLogin : styles.container}>
        <Separator />
        <ServerRequestPage
          data={data}
          errorMessage={errorMessage}
          failure={failure}
          renderView={this.renderContent}
          isFetching={isFetching}
          isInternetConnected={isInternetConnected}
          fetchData={this.fetchData}
          emptyMessage={Strings.noContentFound}
          customStyle={isLogin ? styles.containerLogin : {}}
        />
      </View>
    );
  }
}

const mapStateToProps = store => ({
  cmsContent: store.cmsContent,
  networkInfo: store.networkInfo
});
const actions = { cmsContentRequest, resetCmsContent };

export default connect(
  mapStateToProps,
  actions
)(Content);
